// AudioService - Audio engine for visualizer, PTT, and playback
import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';
import { Capacitor } from '@capacitor/core';

export type AudioOutputMode = 'speaker' | 'earpiece';

@Injectable({
    providedIn: 'root'
})
export class AudioService {
    private audioContext: AudioContext | null = null;
    private analysers = new Map<string, AnalyserNode>();
    private pttStream: MediaStream | null = null;
    private outputMode = new BehaviorSubject<AudioOutputMode>('speaker');

    public outputMode$ = this.outputMode.asObservable();

    constructor() {
        this.initAudioContext();
        this.detectAudioOutput();
    }

    /**
     * Initialize AudioContext
     */
    private initAudioContext(): void {
        this.audioContext = new (window.AudioContext || (window as any).webkitAudioContext)();
    }

    /**
     * Detect audio output mode (Speaker vs Earpiece) for mobile
     * This prevents audio distortion when phone is near ear
     */
    private async detectAudioOutput(): Promise<void> {
        if (Capacitor.isNativePlatform()) {
            // On mobile, we need to detect proximity sensor
            // This requires a Capacitor plugin for proximity detection
            // For now, default to speaker mode
            this.outputMode.next('speaker');

            // TODO: Implement proximity sensor detection
            // When proximity detected (phone near ear), switch to earpiece mode
            // This will be handled by a native plugin
        }
    }

    /**
     * Set audio output mode manually
     */
    setOutputMode(mode: AudioOutputMode): void {
        this.outputMode.next(mode);
        // Apply audio routing changes if on mobile
        if (Capacitor.isNativePlatform()) {
            this.applyAudioRouting(mode);
        }
    }

    /**
     * Apply audio routing for mobile (Speaker/Earpiece)
     */
    private applyAudioRouting(mode: AudioOutputMode): void {
        // This will be implemented via Capacitor native plugin
        // For web/Electron, this is a no-op
        console.log(`[Audio] Routing to: ${mode}`);
    }

    /**
     * Create analyser for ladder bar visualizer
     */
    createAnalyser(channelKey: string, stream: MediaStream): AnalyserNode | null {
        if (!this.audioContext) return null;

        const source = this.audioContext.createMediaStreamSource(stream);
        const analyser = this.audioContext.createAnalyser();

        analyser.fftSize = 256;
        analyser.smoothingTimeConstant = 0.8;

        source.connect(analyser);
        this.analysers.set(channelKey, analyser);

        return analyser;
    }

    /**
     * Get frequency data for visualizer (ladder bar)
     */
    getFrequencyData(channelKey: string): Uint8Array | null {
        const analyser = this.analysers.get(channelKey);
        if (!analyser) return null;

        const dataArray = new Uint8Array(analyser.frequencyBinCount);
        analyser.getByteFrequencyData(dataArray);

        return dataArray;
    }

    /**
     * Get volume level (0-100) for simple meter
     */
    getVolumeLevel(channelKey: string): number {
        const data = this.getFrequencyData(channelKey);
        if (!data) return 0;

        const sum = data.reduce((acc, val) => acc + val, 0);
        const avg = sum / data.length;
        return Math.min(100, Math.round(avg / 255 * 100));
    }

    /**
     * Start PTT (Push-to-Talk) audio capture
     */
    async startPtt(): Promise<MediaStream | null> {
        try {
            this.pttStream = await navigator.mediaDevices.getUserMedia({
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    autoGainControl: true
                }
            });
            return this.pttStream;
        } catch (err) {
            console.error('[Audio] Failed to get microphone:', err);
            return null;
        }
    }

    /**
     * Stop PTT audio capture
     */
    stopPtt(): void {
        if (this.pttStream) {
            this.pttStream.getTracks().forEach(track => track.stop());
            this.pttStream = null;
        }
    }

    /**
     * Play audio file to SIP stream (audio injection)
     */
    async playToStream(audioUrl: string): Promise<AudioBufferSourceNode | null> {
        if (!this.audioContext) return null;

        try {
            const response = await fetch(audioUrl);
            const arrayBuffer = await response.arrayBuffer();
            const audioBuffer = await this.audioContext.decodeAudioData(arrayBuffer);

            const source = this.audioContext.createBufferSource();
            source.buffer = audioBuffer;
            source.connect(this.audioContext.destination);
            source.start(0);

            return source;
        } catch (err) {
            console.error('[Audio] Failed to play audio:', err);
            return null;
        }
    }

    /**
     * Resume AudioContext (required after user interaction)
     */
    async resumeContext(): Promise<void> {
        if (this.audioContext && this.audioContext.state === 'suspended') {
            await this.audioContext.resume();
        }
    }

    /**
     * Cleanup analyser for channel
     */
    removeAnalyser(channelKey: string): void {
        this.analysers.delete(channelKey);
    }

    /**
     * Cleanup all resources
     */
    destroy(): void {
        this.stopPtt();
        this.analysers.clear();
        if (this.audioContext) {
            this.audioContext.close();
            this.audioContext = null;
        }
    }
}
