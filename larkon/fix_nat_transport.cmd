@echo off
REM Add NAT settings to pjsip.conf transport section
echo === Adding NAT settings to transport-udp ===
ssh root@103.154.80.172 "docker exec asterisk bash -c 'cat > /tmp/fix_nat.txt << EOF
[transport-udp]
type=transport
protocol=udp
bind=0.0.0.0:5060
external_media_address=103.154.80.172
external_signaling_address=103.154.80.172
local_net=192.168.0.0/16
local_net=172.16.0.0/12
local_net=10.0.0.0/8

[transport-ws]
type=transport
protocol=ws
bind=0.0.0.0:8089
external_media_address=103.154.80.172
external_signaling_address=103.154.80.172
local_net=192.168.0.0/16
local_net=172.16.0.0/12
local_net=10.0.0.0/8
EOF
'"
echo.
echo === Backup and update pjsip.conf ===
ssh root@103.154.80.172 "docker exec asterisk cp /etc/asterisk/pjsip.conf /etc/asterisk/pjsip.conf.bak"
ssh root@103.154.80.172 "docker exec asterisk bash -c 'head -2 /etc/asterisk/pjsip.conf.bak > /dev/null && sed -i \"1,18d\" /etc/asterisk/pjsip.conf && cat /tmp/fix_nat.txt /etc/asterisk/pjsip.conf > /tmp/pjsip_new.conf && cp /tmp/pjsip_new.conf /etc/asterisk/pjsip.conf'"
echo.
echo === Reload PJSIP ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'module reload res_pjsip.so'"
echo.
echo === Verify transport ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show transports'"
