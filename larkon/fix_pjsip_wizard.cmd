@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo === Fix pjsip_wizard.conf for all endpoints ===
echo Adding media_encryption_optimistic=yes for non-WebRTC bridging

"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c 'cat > /etc/asterisk/pjsip_wizard.conf << EOF
; Auto-generated pjsip_wizard configuration
; Allows WebRTC <-> Non-WebRTC bridging

[global-endpoint](!)
type = wizard
transport = transport-ws,transport-wss,transport-udp
endpoint/context = internal
endpoint/allow = opus,g722,ulaw,alaw
endpoint/direct_media = no
endpoint/rtp_symmetric = yes
endpoint/force_rport = yes
endpoint/rewrite_contact = yes
endpoint/media_encryption_optimistic = yes
endpoint/rtp_keepalive = 5
aor/max_contacts = 5
aor/remove_existing = yes
aor/remove_unavailable = yes

[webrtc-endpoint](!,global-endpoint)
endpoint/webrtc = yes
endpoint/dtls_auto_generate_cert = yes
endpoint/media_encryption = dtls
endpoint/ice_support = yes
endpoint/use_avpf = yes
endpoint/rtcp_mux = yes

[softphone-endpoint](!,global-endpoint)
endpoint/webrtc = no
endpoint/media_encryption = no
endpoint/ice_support = no
endpoint/use_avpf = no

; WebRTC Extensions (from browser)
[6000](webrtc-endpoint)
inbound_auth/username = 6000
inbound_auth/password = Maja1234

[6001](webrtc-endpoint)
inbound_auth/username = 6001
inbound_auth/password = Maja1234

[6002](webrtc-endpoint)
inbound_auth/username = 6002
inbound_auth/password = Maja1234

[6003](webrtc-endpoint)
inbound_auth/username = 6003
inbound_auth/password = Maja1234

; Softphone Extensions (Phoner/Zoiper)
[6010](softphone-endpoint)
inbound_auth/username = 6010
inbound_auth/password = Maja1234

[6011](softphone-endpoint)
inbound_auth/username = 6011
inbound_auth/password = Maja1234

[6012](softphone-endpoint)
inbound_auth/username = 6012
inbound_auth/password = Maja1234
EOF'"

echo.
echo === Reload pjsip ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'module reload res_pjsip.so'"

echo.
echo === Check endpoints ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoints' 2>&1 | head -25"

echo DONE
