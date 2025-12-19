@echo off
echo [1/3] Building Ionic Project...
call npm run build

echo [2/3] Building Electron Assets...
call npm run electron:build

echo [3/3] Launching Electron App...
npx electron .

@echo off
echo [INFO] Searching phonebook logic in script.js...
findstr /n "phonebook contact-card renderPhonebook" "c:\wamp64\www\SmartX\client\script.js" > phonebook_logic.txt
echo [DONE] Logic saved to phonebook_logic.txt

pause