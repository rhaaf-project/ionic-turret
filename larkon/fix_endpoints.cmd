@echo off
REM Add rewrite_contact and rtp_symmetric to all endpoints
echo === Adding NAT options to all endpoints ===
ssh root@103.154.80.172 "docker exec asterisk sed -i '/type=endpoint/a force_rport=yes\nrewrite_contact=yes\nrtp_symmetric=yes' /etc/asterisk/pjsip.conf"
echo.
echo === Reload PJSIP ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip reload'"
echo.
echo === Verify 6010 endpoint settings ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6010' | grep -E '(rewrite|rtp_symmetric|force_rport)'"
