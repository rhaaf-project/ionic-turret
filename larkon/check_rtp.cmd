@echo off
echo === Check RTP Configuration ===
ssh root@103.154.80.172 "docker exec asterisk cat /etc/asterisk/rtp.conf"
echo.
echo === Check PJSIP Media Settings ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6000' | findstr -i 'media\|rtp\|ice\|dtls'"
echo.
echo === Check 6010 Endpoint Settings ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6010' | findstr -i 'media\|rtp\|ice\|dtls\|webrtc'"
echo.
echo === Check Recent Asterisk Logs for RTP Issues ===
ssh root@103.154.80.172 "docker exec asterisk tail -100 /var/log/asterisk/full | grep -i 'rtp\|ice\|dtls\|media'"
