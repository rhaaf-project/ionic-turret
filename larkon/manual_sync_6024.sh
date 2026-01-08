#!/bin/bash
# Manual sync extension 6024 to Asterisk - run as www-data
cd /var/www/html/smartX

# Get extension data
EXT="6024"
SECRET=$(sudo -u www-data php artisan tinker --execute="echo App\Models\Extension::where('extension','$EXT')->first()->secret ?? 'Maja1234';")
TYPE=$(sudo -u www-data php artisan tinker --execute="echo App\Models\Extension::where('extension','$EXT')->first()->type ?? 'webrtc';")

echo "Extension: $EXT"
echo "Type: $TYPE"

# Generate config based on type
if [ "$TYPE" == "softphone" ]; then
    CONFIG="
[$EXT]
type=endpoint
context=internal
disallow=all
allow=opus,g722,ulaw,alaw
auth=$EXT
aors=$EXT
webrtc=no
rtp_symmetric=yes
force_rport=yes
rewrite_contact=yes
direct_media=no
media_encryption=no
media_encryption_optimistic=no
ice_support=no
dtls_auto_generate_cert=no

[$EXT]
type=auth
auth_type=userpass
username=$EXT
password=$SECRET

[$EXT]
type=aor
max_contacts=5
remove_existing=yes
"
else
    CONFIG="
[$EXT]
type=endpoint
context=internal
disallow=all
allow=opus,g722,ulaw,alaw
auth=$EXT
aors=$EXT
webrtc=yes
dtls_auto_generate_cert=yes
rtp_symmetric=yes
force_rport=yes
rewrite_contact=yes
direct_media=no
media_encryption_optimistic=yes

[$EXT]
type=auth
auth_type=userpass
username=$EXT
password=$SECRET

[$EXT]
type=aor
max_contacts=5
remove_existing=yes
"
fi

# Write to temp file
echo "$CONFIG" > /tmp/ext_${EXT}.conf
echo "Created /tmp/ext_${EXT}.conf"

# SCP to 172
scp -o StrictHostKeyChecking=no /tmp/ext_${EXT}.conf root@103.154.80.172:/tmp/
echo "SCP done"

# Copy into docker and append
ssh -o StrictHostKeyChecking=no root@103.154.80.172 "docker cp /tmp/ext_${EXT}.conf asterisk:/tmp/ && docker exec asterisk sh -c 'cat /tmp/ext_${EXT}.conf >> /etc/asterisk/pjsip.conf' && docker exec asterisk asterisk -rx 'pjsip reload'"
echo "Reload done"

# Verify
ssh -o StrictHostKeyChecking=no root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint $EXT' | head -5"

# Cleanup
rm /tmp/ext_${EXT}.conf
ssh -o StrictHostKeyChecking=no root@103.154.80.172 "rm /tmp/ext_${EXT}.conf; docker exec asterisk rm /tmp/ext_${EXT}.conf"
echo "Done!"
