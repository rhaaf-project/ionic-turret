@echo off
REM Add NAT settings to pjsip_wizard.conf for proper NAT traversal
echo === Adding NAT support settings ===
ssh root@103.154.80.172 "docker exec asterisk bash -c 'for ext in 6000 6001 6002 6003 6004 6005 6006 6007 6008 6009 6010 6011 6012 6013 6014 6015 6016 6017 6018 6019 6020; do sed -i \"/\[${ext}\]/a endpoint/rtp_symmetric = yes\nendpoint/force_rport = yes\nendpoint/rewrite_contact = yes\nendpoint/direct_media = no\" /etc/asterisk/pjsip_wizard.conf; done'"
echo.
echo === Reload PJSIP ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip reload'"
echo.
echo === Verify 6010 settings ===
ssh root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 6010' | grep -E '(rtp_symmetric|force_rport|rewrite_contact|direct_media)'"
