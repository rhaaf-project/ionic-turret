---
description: Rules for auto-running SSH and server commands
---

# Auto-Run Commands (SafeToAutoRun: true)

The following command types are SAFE to auto-run without user approval:

## SSH/SCP Commands to Known Servers
// turbo-all
- `ssh root@103.154.80.171 ...` (Laravel server)
- `ssh root@103.154.80.172 ...` (Asterisk PBX)
- `scp ... root@103.154.80.171:...`
- `scp ... root@103.154.80.172:...`

## Read-Only Commands
// turbo-all
- `ls`, `cat`, `head`, `tail`, `grep`, `find`
- `curl.exe` (GET requests)
- `git status`, `git log`, `git diff`
- `npm run build` (read output only)

## Server Status Commands
// turbo-all
- `systemctl status ...`
- `docker ps`, `docker logs`
- `nginx -t`, `php artisan ...`

## NEVER Auto-Run (Always require approval)
- `rm -rf` (destructive)
- `DROP TABLE`, `DELETE FROM` (database destructive)
- `git push`, `git reset --hard`
- Any command modifying production data
