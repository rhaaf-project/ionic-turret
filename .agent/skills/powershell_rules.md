# Shell Escaping & Scripting Guidelines
Description: Rules to prevent PowerShell escaping errors and ensure command execution stability on Windows.

## 1. Prefer Scripting Over One-Liners
Jika perintah melibatkan logika kompleks, manipulasi string berat, atau lebih dari dua level tanda kutip, DILARANG menggunakan PowerShell one-liner.

Gunakan script PHP minimal (clean) atau Node.js untuk mengeksekusi logika tersebut.

Simpan sebagai file temporer, jalankan, lalu hapus jika sudah selesai.

## 2. PowerShell Escaping Rules
Gunakan Here-Strings @' ... '@ jika harus mengirim blok teks besar agar tidak perlu melakukan manual escaping pada setiap karakter spesial.

Selalu gunakan backtick (`) untuk escape karakter khusus PowerShell seperti $, ", ', atau ( jika berada di dalam double quotes.

## 3. Context Awareness
Ingat bahwa user adalah seorang SysAdmin dan Developer yang bekerja di lingkungan Windows namun mengelola server Linux (Debian/Proxmox).


## 4. Specific Lessons Learned & Anticipations
- **SSH + PowerShell + Bash Hell**: Saat menjalankan command via SSH dari PowerShell, escaping menjadi 3 lapis (PowerShell -> SSH client -> Remote Shell).
  - *JANGAN* mencoba escape regex `sed` yang kompleks via SSH inline. Pasti gagal.
  - *JANGAN* gunakan `php -r "..."` yang mengandung variabel (`$var`) atau quote ganda via SSH.
  - **SOLUSI**: Tulis script di file lokal -> `scp` ke server -> `ssh` execute php script -> hapus file. Ini 100% works dan hemat waktu debugging.
- **Quote Traps**:
  - PowerShell menganggap backslash `\` sebagai karakter literal di dalam single quote `'...'`, tapi SSH/Bash di ujung sana mungkin butuh escape tambahan.
  - Daripada menebak jumlah backslash `\\\\`, GUNAKAN FILE SCRIPT.

