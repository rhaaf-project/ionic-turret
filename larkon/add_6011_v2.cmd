@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR

echo === Adding 6011 endpoint to pjsip.conf ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo '' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo '[6011]' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo 'type=endpoint' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo 'force_rport=yes' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo 'rewrite_contact=yes' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo 'rtp_symmetric=yes' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo 'context=internal' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo 'disallow=all' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo 'allow=opus,g722,ulaw,alaw' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo 'auth=6011' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo 'aors=6011' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo 'direct_media=no' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo 'media_encryption=no' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo 'media_encryption_optimistic=yes' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo '' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo '[6011]' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo 'type=auth' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo 'auth_type=userpass' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo 'password=Maja1234' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo 'username=6011' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo '' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo '[6011]' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo 'type=aor' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo 'max_contacts=5' >> /etc/asterisk/pjsip.conf\""
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk bash -c \"echo 'remove_existing=yes' >> /etc/asterisk/pjsip.conf\""

echo.
echo === Reload pjsip ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'module reload res_pjsip.so'"

echo.
echo === Verify 6011 exists ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6011' 2>&1 | grep -E '(Endpoint|context|media_encryption)'"

echo DONE
