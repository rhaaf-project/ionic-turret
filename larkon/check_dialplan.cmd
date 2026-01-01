@echo off
REM Check current dialplan
echo === Current dialplan ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'dialplan show internal'"
