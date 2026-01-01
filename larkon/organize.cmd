@echo off
setlocal

cd /d "d:\02____WORKS\04___Server\Projects\ionic\trader-turret\larkon"

REM Create _notuse folder
mkdir "_notuse" 2>nul

REM Move cleanup scripts (temporary)
move "cleanup2.cmd" "_notuse\" 2>nul
move "cleanup_larkon.cmd" "_notuse\" 2>nul
move "check_extension_sync.cmd" "_notuse\" 2>nul
move "check_asterisk_service.cmd" "_notuse\" 2>nul

REM Move one-time fix scripts (already used)
move "fix_demo1_pw.php" "_notuse\" 2>nul

echo.
echo Moved scripts to _notuse folder.
echo.
dir /B "_notuse" 2>nul
echo.
echo === Remaining in larkon: ===
dir /B *.cmd *.php 2>nul | find /c /v ""
