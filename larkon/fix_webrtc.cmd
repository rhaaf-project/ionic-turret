@echo off
REM Fix: Remove webrtc=yes from softphone extensions
echo === Remove webrtc from 6007 and 6010 ===
ssh root@103.154.80.172 "docker exec asterisk sed -i '/^\[6010\]/,/^\[/{/webrtc=yes/d; /dtls_auto_generate_cert=yes/d}' /etc/asterisk/pjsip.conf"
ssh root@103.154.80.172 "docker exec asterisk sed -i '/^\[6007\]/,/^\[/{/webrtc=yes/d; /dtls_auto_generate_cert=yes/d}' /etc/asterisk/pjsip.conf"
echo.
echo === Also fix ICE on browser side - reduce STUN timeout ===
REM This is done in JsSIP config instead
echo.
echo === Reload PJSIP ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip reload'"
echo.
echo === Verify 6010 has no webrtc ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6010' | grep -E '(webrtc|dtls)'"
