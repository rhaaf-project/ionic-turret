@echo off
REM Upload extensions.conf to Asterisk
echo === Upload to server 172 ===
scp "D:/02____WORKS/04___Server/Projects/ionic/trader-turret/larkon/extensions.conf" root@103.154.80.172:/tmp/
echo.
echo === Copy into docker ===
ssh root@103.154.80.172 "docker cp /tmp/extensions.conf asterisk:/etc/asterisk/"
echo.
echo === Reload dialplan ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'dialplan reload'"
echo.
echo === Verify 6011 now works ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'dialplan show 6011@internal'"
