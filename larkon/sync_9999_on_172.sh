#!/bin/bash
# Complete sync test: Create 9999 and sync to Asterisk
# Run on server 172 directly

set -e

EXT="9999"
echo "=== Syncing extension $EXT to Asterisk ==="

# 1. Create config file on host
cat > /tmp/ext_${EXT}.conf << 'EOF'

[9999]
type=endpoint
context=internal
disallow=all
allow=opus,g722,ulaw,alaw
auth=9999
aors=9999
webrtc=yes
dtls_auto_generate_cert=yes
rtp_symmetric=yes
force_rport=yes
rewrite_contact=yes
direct_media=no
media_encryption_optimistic=yes

[9999]
type=auth
auth_type=userpass
username=9999
password=Maja1234

[9999]
type=aor
max_contacts=5
remove_existing=yes
EOF

echo "1. Config file created"

# 2. Copy current pjsip.conf from container
docker cp asterisk:/etc/asterisk/pjsip.conf /tmp/pjsip_current.conf
echo "2. Copied current pjsip.conf ($(wc -l < /tmp/pjsip_current.conf) lines)"

# 3. Append new extension
cat /tmp/ext_${EXT}.conf >> /tmp/pjsip_current.conf
echo "3. Appended extension config"

# 4. Copy back to container
docker cp /tmp/pjsip_current.conf asterisk:/etc/asterisk/pjsip.conf
echo "4. Copied back to container"

# 5. Reload PJSIP
docker exec asterisk asterisk -rx 'pjsip reload'
echo "5. Reloaded PJSIP"

# 6. Verify
echo "6. Verifying..."
docker exec asterisk asterisk -rx "pjsip show endpoint $EXT" | head -10

# 7. Count total endpoints
echo ""
docker exec asterisk asterisk -rx 'pjsip show endpoints' | grep "Objects found"

# Cleanup
rm /tmp/ext_${EXT}.conf /tmp/pjsip_current.conf

echo "=== DONE ==="
