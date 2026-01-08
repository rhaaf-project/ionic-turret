#!/bin/bash
# Test auto-sync: Create extension 8888 via Laravel Model (triggers sync)
cd /var/www/html/smartX

echo "=== Before: Check Asterisk endpoints ==="
ssh -o StrictHostKeyChecking=no root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoints' | grep 'Objects'"

echo ""
echo "=== Creating extension 8888 via Laravel ==="
php artisan tinker --execute='
$ext = new App\Models\Extension();
$ext->extension = "8888";
$ext->type = "softphone";
$ext->name = "Test Softphone Auto";
$ext->secret = "Maja1234";
$ext->context = "internal";
$ext->is_active = true;
$ext->save();
echo "Created extension 8888\n";
'

echo ""
echo "=== After: Check Asterisk endpoints ==="
ssh -o StrictHostKeyChecking=no root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoint 8888' | head -5"
ssh -o StrictHostKeyChecking=no root@103.154.80.172 "docker exec asterisk asterisk -rx 'pjsip show endpoints' | grep 'Objects'"

echo ""
echo "=== Check Laravel logs ==="
tail -10 /var/www/html/smartX/storage/logs/laravel.log | grep -E '(8888|Asterisk)'

echo "=== DONE ==="
