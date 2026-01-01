@echo off
setlocal ENABLEEXTENSIONS

set SSH_BIN=C:\Windows\System32\OpenSSH\ssh.exe
set SSH_OPTS=-o StrictHostKeyChecking=no -o UserKnownHostsFile=NUL -o BatchMode=yes -o PreferredAuthentications=publickey -o LogLevel=ERROR -o ConnectTimeout=10

echo === Getting Asterisk verbose logs (last 100 lines) ===
"%SSH_BIN%" %SSH_OPTS% root@103.154.80.172 "docker logs asterisk --tail=100 2>&1"

echo DONE
