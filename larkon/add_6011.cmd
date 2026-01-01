@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo === Add 6011 endpoint to pjsip.conf ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c 'cat >> /etc/asterisk/pjsip.conf << EOF

[6011]
type=endpoint
force_rport=yes
rewrite_contact=yes
rtp_symmetric=yes
context=internal
disallow=all
allow=opus,g722,ulaw,alaw
auth=6011
aors=6011
direct_media=no
webrtc=no
media_encryption=no
media_encryption_optimistic=yes
ice_support=no

[6011]
type=auth
auth_type=userpass
password=Maja1234
username=6011

[6011]
type=aor
max_contacts=5
remove_existing=yes
EOF
'"

echo.
echo === Reload pjsip ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'module reload res_pjsip.so'"

echo.
echo === Verify 6011 endpoint exists ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6011' 2>&1 | head -5"

echo.
echo === Check registered contacts ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show contacts'"

echo DONE - Now restart Zoiper and test call again!
