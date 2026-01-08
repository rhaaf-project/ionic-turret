#!/bin/bash
# Fix extension types in database

PGPASSWORD=smartx2025 psql -h 127.0.0.1 -U postgres -d smartx_db << 'EOF'
-- Set WebRTC for 6000-6009
UPDATE extensions SET type = 'webrtc' WHERE extension IN ('6000','6001','6002','6003','6004','6005','6006','6007','6008','6009');

-- Set Softphone for 6010+ and 7022
UPDATE extensions SET type = 'softphone' WHERE extension NOT IN ('6000','6001','6002','6003','6004','6005','6006','6007','6008','6009');

-- Show result
SELECT extension, type, name FROM extensions ORDER BY extension;
EOF
