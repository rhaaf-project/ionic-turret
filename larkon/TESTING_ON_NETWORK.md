# Testing on Network

## Quick Start
Run dev server (already configured for network access):
```bash
ionic serve
# or
ng serve
```

## Access from Other Devices
1. Check laptop IP:
   ```bash
   ipconfig | findstr /i "IPv4"
   ```

2. From other device on same WiFi, open:
   ```
   http://<LAPTOP_IP>:4200/turret
   ```

## Network Segments
- **Home:** `192.168.1.x`
- **Office:** `192.168.100.x`

Config in `angular.json` uses `host: "0.0.0.0"` which binds to ALL interfaces automatically.

## Troubleshooting
If can't access:
1. Check Windows Firewall allows port 4200
2. Ensure both devices on same network/WiFi
3. Try disabling VPN if active
