@echo off
REM Check current roles and create CMS admin
ssh root@103.154.80.171 "PGPASSWORD=smartx2025 psql -h 127.0.0.1 -U postgres -d smartx_db -c 'SELECT id, name, email, use_ext, role FROM users ORDER BY id;'"
