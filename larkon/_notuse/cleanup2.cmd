@echo off
setlocal

cd /d "d:\02____WORKS\04___Server\Projects\ionic\trader-turret\larkon"

REM === Delete more duplicate/old scripts ===

REM Old debug scripts
del /Q "debug_dialplan2.cmd" 2>nul
del /Q "debug_phoner.cmd" 2>nul

REM Old fix scripts - keep only essential
del /Q "fix_nat2.cmd" 2>nul
del /Q "fix_nat3.cmd" 2>nul
del /Q "fix_dialplan2.cmd" 2>nul

REM Old check scripts - keep only useful
del /Q "check_6008.cmd" 2>nul
del /Q "check_6010.cmd" 2>nul
del /Q "check_6011_reg.cmd" 2>nul
del /Q "check_docker_network.cmd" 2>nul
del /Q "check_udp.cmd" 2>nul
del /Q "check_wizard.cmd" 2>nul

REM Old test scripts
del /Q "test_call_6008.cmd" 2>nul
del /Q "test_call_6010.cmd" 2>nul
del /Q "test_qualify.cmd" 2>nul

REM Cleanup this and previous cleanup script
del /Q "cleanup_larkon.cmd" 2>nul

echo Final cleanup done!
echo.
echo === Remaining files ===
dir /B *.cmd *.php 2>nul
