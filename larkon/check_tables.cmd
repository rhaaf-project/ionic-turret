@echo off
REM Check all tables in database and look for admin/role related tables
ssh root@103.154.80.171 "PGPASSWORD=smartx2025 psql -h 127.0.0.1 -U postgres -d smartx_db -c '\dt'"
