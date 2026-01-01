@echo off
REM Quick verify NAT settings are now active
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6010' | grep -E '(rtp_symmetric|rewrite_contact|direct_media)'"
