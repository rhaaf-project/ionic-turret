#!/bin/bash
# Reset admin password to 'admin123'
# Laravel's default test hash for 'password' is used, which works for 'password'
# We need to generate a proper hash

cd /var/www/html/smartX
php -r "
\$hash = password_hash('admin123', PASSWORD_BCRYPT);
echo \"Generated hash: \$hash\n\";
file_put_contents('/tmp/admin_hash.txt', \$hash);
"

HASH=$(cat /tmp/admin_hash.txt)
echo "Using hash: $HASH"

PGPASSWORD=smartx2025 psql -h 127.0.0.1 -U postgres -d smartx_db -c "UPDATE users SET password = '$HASH' WHERE email = 'admin@smartx.local';"

echo "Password reset complete for admin@smartx.local"
