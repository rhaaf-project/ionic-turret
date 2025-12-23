@echo off
set CI=true
set IONIC_TELEMETRY=0

echo [1/3] Building Ionic Project...
:: Cara ini: Masuk ke PowerShell -> Kirim 'y' -> Jalankan NPM -> Ambil 5 baris terakhir
powershell -Command "& {echo 'y' | npm run build -- --confirm 2>&1 | Select-Object -Last 5}"

echo [2/3] Building Electron Assets...
echo y | call npm run electron:build -- --confirm

echo [3/3] Launching Electron App...
npx electron .

@echo off
echo [INFO] Searching phonebook logic in script.js...
findstr /n "phonebook contact-card renderPhonebook" "c:\wamp64\www\SmartX\client\script.js" > phonebook_logic.txt
echo [DONE] Logic saved to phonebook_logic.txt

pause