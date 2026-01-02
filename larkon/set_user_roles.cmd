@echo off
REM Set roles: turret users = 'user', create CMS admin
ssh root@103.154.80.171 "PGPASSWORD=smartx2025 psql -h 127.0.0.1 -U postgres -d smartx_db -c \"UPDATE users SET role = 'user' WHERE role IS NULL OR role = '';\""
