// SipService - JsSIP integration for SIP/WSS communication
import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';
import * as JsSIP from 'jssip';

export interface SipConfig {
    server: string;
    port: number;
    extension: string;
    password: string;
}

export interface SipSession {
    channelKey: string;
    session: any; // JsSIP.RTCSession - using any due to incomplete type exports
    direction: 'incoming' | 'outgoing';
    state: 'connecting' | 'connected' | 'ended';
}

@Injectable({
    providedIn: 'root'
})
export class SipService {
    private ua: JsSIP.UA | null = null;
    private activeSessions = new BehaviorSubject<Map<string, SipSession>>(new Map());
    private registrationStatus = new BehaviorSubject<'unregistered' | 'registering' | 'registered' | 'failed'>('unregistered');
    private lineBusyStatus = new BehaviorSubject<Map<string, boolean>>(new Map());
    private incomingCall = new BehaviorSubject<{ number: string, channelKey: string } | null>(null);

    public activeSessions$ = this.activeSessions.asObservable();
    public registrationStatus$ = this.registrationStatus.asObservable();
    public lineBusyStatus$ = this.lineBusyStatus.asObservable();
    public incomingCall$ = this.incomingCall.asObservable();

    constructor() {
        // Enable JsSIP debug in development
        JsSIP.debug.enable('JsSIP:*');
    }

    /**
     * Register extension to PBX via WebSocket
     */
    registerExtension(config: SipConfig): void {
        const wsUri = `wss://${config.server}:${config.port}/ws`;
        const sipUri = `sip:${config.extension}@${config.server}`;

        const socket = new JsSIP.WebSocketInterface(wsUri);

        const uaConfig = {
            sockets: [socket],
            uri: sipUri,
            password: config.password,
            register: true,
            session_timers: false
        };

        this.ua = new JsSIP.UA(uaConfig);

        this.ua.on('registered', () => {
            console.log(`[SIP] Registered: ${config.extension}`);
            this.registrationStatus.next('registered');
        });

        this.ua.on('unregistered', () => {
            console.log(`[SIP] Unregistered: ${config.extension}`);
            this.registrationStatus.next('unregistered');
        });

        this.ua.on('registrationFailed', (e: any) => {
            console.error(`[SIP] Registration failed: ${e.cause}`);
            this.registrationStatus.next('failed');
        });

        this.ua.on('newRTCSession', (data: any) => {
            this.handleNewSession(data);
        });

        this.registrationStatus.next('registering');
        this.ua.start();
    }

    /**
     * Make outgoing call
     */
    makeCall(targetUri: string, channelKey: string): void {
        if (!this.ua) {
            console.error('[SIP] UA not initialized');
            return;
        }

        console.log(`[SIP] Making call to ${targetUri} for channel ${channelKey}`);

        const options = {
            mediaConstraints: { audio: true, video: false },
            rtcOfferConstraints: { offerToReceiveAudio: true, offerToReceiveVideo: false },
            pcConfig: {
                iceServers: [{ urls: 'stun:stun.l.google.com:19302' }]
            }
        };

        const session = this.ua.call(targetUri, options);

        // [FIX] Setup audio playback like SmartX
        session.on('peerconnection', (e: any) => {
            console.log('[SIP] Peerconnection established');
            e.peerconnection.ontrack = (event: RTCTrackEvent) => {
                console.log('[SIP] Remote track received');
                const remoteAudio = new Audio();
                remoteAudio.srcObject = event.streams[0];
                remoteAudio.play().catch(err => {
                    console.warn('[SIP] Audio autoplay blocked:', err);
                });
            };
        });

        session.on('progress', () => {
            console.log(`[SIP] Progress (ringing): ${channelKey}`);
        });

        this.addSession(channelKey, session, 'outgoing');
    }

    /**
     * Answer incoming call
     */
    answerCall(channelKey: string): void {
        const sessions = this.activeSessions.getValue();
        const sipSession = sessions.get(channelKey);

        if (sipSession && sipSession.direction === 'incoming') {
            sipSession.session.answer({
                mediaConstraints: { audio: true, video: false }
            });
        }
    }

    /**
     * Hangup call
     */
    hangupCall(channelKey: string): void {
        const sessions = this.activeSessions.getValue();
        const sipSession = sessions.get(channelKey);

        if (sipSession) {
            sipSession.session.terminate();
            this.removeSession(channelKey);
        }
    }

    /**
     * Send DTMF tone
     */
    sendDTMF(channelKey: string, tone: string): void {
        const sessions = this.activeSessions.getValue();
        const sipSession = sessions.get(channelKey);

        if (sipSession) {
            sipSession.session.sendDTMF(tone);
        }
    }

    /**
     * Get audio stream from session for visualizer
     */
    getRemoteAudioStream(channelKey: string): MediaStream | null {
        const sessions = this.activeSessions.getValue();
        const sipSession = sessions.get(channelKey);

        if (sipSession) {
            const connection = sipSession.session.connection;
            if (connection) {
                const receivers = connection.getReceivers();
                const audioReceiver = receivers.find((r: RTCRtpReceiver) => r.track.kind === 'audio');
                if (audioReceiver) {
                    return new MediaStream([audioReceiver.track]);
                }
            }
        }
        return null;
    }

    /**
     * Disconnect and cleanup
     */
    disconnect(): void {
        if (this.ua) {
            this.ua.stop();
            this.ua = null;
        }
        this.activeSessions.next(new Map());
        this.registrationStatus.next('unregistered');
    }

    /**
     * Unregister from PBX (alias for disconnect)
     */
    unregister(): void {
        this.disconnect();
    }

    private handleNewSession(data: any): void {
        const { originator, session, request } = data;
        const direction: 'incoming' | 'outgoing' = originator === 'remote' ? 'incoming' : 'outgoing';

        // For incoming calls, get caller number from request
        const channelKey = `incoming-${Date.now()}`;

        if (direction === 'incoming') {
            const callerNumber = request?.from?.uri?.user || 'Unknown';
            console.log(`[SIP] Incoming call from: ${callerNumber}`);

            // Emit incoming call for UI notification
            this.incomingCall.next({ number: callerNumber, channelKey });

            // Auto-play ringtone (could be enhanced)
            session.on('ended', () => {
                this.incomingCall.next(null);
            });
            session.on('failed', () => {
                this.incomingCall.next(null);
            });
        }

        this.addSession(channelKey, session, direction);
    }

    private addSession(channelKey: string, session: any, direction: 'incoming' | 'outgoing'): void {
        const sipSession: SipSession = {
            channelKey,
            session,
            direction,
            state: 'connecting'
        };

        session.on('confirmed', () => {
            sipSession.state = 'connected';
            this.updateSession(channelKey, sipSession);
        });

        session.on('ended', () => {
            console.log(`[SIP] Session ended: ${channelKey}`);
            this.removeSession(channelKey);
        });

        session.on('failed', (e: any) => {
            console.log(`[SIP] Session failed: ${channelKey}`, e?.cause || '');
            this.removeSession(channelKey);
        });

        const sessions = new Map(this.activeSessions.getValue());
        sessions.set(channelKey, sipSession);
        this.activeSessions.next(sessions);
    }

    private updateSession(channelKey: string, sipSession: SipSession): void {
        const sessions = new Map(this.activeSessions.getValue());
        sessions.set(channelKey, sipSession);
        this.activeSessions.next(sessions);
    }

    private removeSession(channelKey: string): void {
        const sessions = new Map(this.activeSessions.getValue());
        sessions.delete(channelKey);
        this.activeSessions.next(sessions);
    }
}
