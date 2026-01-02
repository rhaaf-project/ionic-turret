@echo off
REM Delete duplicate admin user (ID 10)
ssh root@103.154.80.171 "PGPASSWORD=smartx2025 psql -h 127.0.0.1 -U postgres -d smartx_db -c 'DELETE FROM users WHERE id = 10;'"
echo Deleted user ID 10
