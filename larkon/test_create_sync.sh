#!/bin/bash
# Create new extension 9999 and sync to Asterisk

# Step 1: Create in database
echo "=== Step 1: Creating extension 9999 in database ==="
PGPASSWORD=smartx2025 psql -h 127.0.0.1 -U postgres -d smartx_db -c "INSERT INTO extensions (extension, type, name, secret, context, is_active, created_at, updated_at) VALUES ('9999', 'webrtc', 'Test Auto Sync', 'Maja1234', 'internal', true, NOW(), NOW()) ON CONFLICT (extension) DO NOTHING;"
PGPASSWORD=smartx2025 psql -h 127.0.0.1 -U postgres -d smartx_db -c "SELECT extension, type, name FROM extensions WHERE extension='9999';"

# Step 2: Generate WebRTC config
echo ""
echo "=== Step 2: Generating WebRTC config ==="
cat > /tmp/ext_9999.conf << 'EOF'
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
echo "Config file created"
cat /tmp/ext_9999.conf

# Step 3: SCP to 172
echo ""
echo "=== Step 3: Copying to server 172 ==="
scp -o StrictHostKeyChecking=no /tmp/ext_9999.conf root@103.154.80.172:/tmp/

# Step 4: Append to pjsip.conf and reload
echo ""
echo "=== Step 4: Appending to pjsip.conf and reloading ==="
ssh -o StrictHostKeyChecking=no root@103.154.80.172 "docker cp /tmp/ext_9999.conf asterisk:/tmp/ && docker exec asterisk sh -c 'cat /tmp/ext_9999.conf >> /etc/asterisk/pjsip.conf' && docker exec asterisk asterisk -rx 'pjsip reload'"

# Step 5: Verify
echo ""
echo "=== Step 5: Verifying ==="
ssh -o StrictHostKeyChecking=no root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 9999'"

# Cleanup
rm /tmp/ext_9999.conf
ssh -o StrictHostKeyChecking=no root@103.154.80.172 "rm /tmp/ext_9999.conf; docker exec asterisk rm /tmp/ext_9999.conf 2>/dev/null"

echo ""
echo "=== DONE ==="
