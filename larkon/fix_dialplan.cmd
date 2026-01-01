@echo off
REM Fix extensions.conf dialplan
echo === Fixing extensions.conf ===
ssh root@103.154.80.172 "docker exec asterisk bash -c 'cat > /etc/asterisk/extensions.conf << EOF
[general]
static=yes
writeprotect=no

[internal]
exten => 6000,1,Dial(PJSIP/6000,30)
 same => n,Hangup()

exten => 6001,1,Dial(PJSIP/6001,30)
 same => n,Hangup()

exten => 6002,1,Dial(PJSIP/6002,30)
 same => n,Hangup()

exten => 6003,1,Dial(PJSIP/6003,30)
 same => n,Hangup()

; Pattern for 6004-6009
exten => _600X,1,Dial(PJSIP/\${EXTEN},30)
 same => n,Hangup()

; Pattern for 6010-6099
exten => _60XX,1,Dial(PJSIP/\${EXTEN},30)
 same => n,Hangup()
EOF'"
echo.
echo === Reloading dialplan ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'dialplan reload'"
echo.
echo === Verify 6011 routing ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'dialplan show 6011@internal'"
