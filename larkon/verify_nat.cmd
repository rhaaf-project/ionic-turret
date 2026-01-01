@echo off
REM Verify NAT settings on 6010
echo === Verify 6010 NAT settings ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6010' | grep -E '(rtp_symmetric|force_rport|rewrite_contact|direct_media|context)'"
