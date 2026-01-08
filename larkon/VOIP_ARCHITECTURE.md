# SmartUCX VoIP Architecture Guide
> Last Updated: 2026-01-08 by Antigravity Agent

---

## 1. SIP Stack Overview

### Arsitektur 3-Layer

```
┌─────────────────────────────────────────────────────────────────┐
│                     BROWSER (Ionic Turret)                      │
│                         ↓ JsSIP ↓                               │
│              JavaScript SIP Library over WebSocket              │
└─────────────────────────────────────────────────────────────────┘
                              ↓ WSS/WS
┌─────────────────────────────────────────────────────────────────┐
│                   SERVER 172 (Asterisk PBX)                     │
│                        ↓ PJSIP ↓                                │
│         Asterisk Channel Driver (chan_pjsip)                    │
│         - Handles WebRTC ↔ SIP bridging                         │
│         - SRTP ↔ RTP transcoding                                │
└─────────────────────────────────────────────────────────────────┘
                              ↓ UDP/TCP
┌─────────────────────────────────────────────────────────────────┐
│                   SOFTPHONES (Zoiper/Phoner)                    │
│              Standard SIP over UDP (port 5060)                  │
└─────────────────────────────────────────────────────────────────┘
```

### Penjelasan Komponen

| Komponen | Lokasi | Fungsi |
|----------|--------|--------|
| **JsSIP** | Browser (npm package) | JavaScript library untuk SIP signaling via WebSocket |
| **PJSIP** | Asterisk (chan_pjsip) | Asterisk module untuk handle SIP endpoints |
| **WebSocket** | Port 8089 | Transport untuk WebRTC signaling |
| **UDP** | Port 5060 | Transport untuk softphone tradisional |

### Jadi Pakai Apa?

- **Browser** → **JsSIP** (library js, bukan protocol)
- **Asterisk** → **PJSIP** (bukan chan_sip yang deprecated)
- **Softphone** → **Standard SIP** over UDP

---

## 2. Docker Network Mode

### Current: Host Mode (`--net=host`)

```bash
# Container menggunakan network stack host langsung
docker run --net=host asterisk
```

**Keuntungan untuk VoIP:**
- ✅ Tidak ada NAT translation internal
- ✅ RTP ports (10000-20000) otomatis terbuka
- ✅ `external_media_address` langsung pointing ke IP server
- ✅ Tidak ada one-way audio issues

**Verifikasi:**
```bash
docker inspect asterisk --format '{{.HostConfig.NetworkMode}}'
# Output: host
```

### Kenapa Tidak Bridge?

| Issue | Bridge Mode | Host Mode |
|-------|-------------|-----------|
| RTP Path | Via Docker NAT → sering one-way audio | Direct → OK |
| Port Mapping | 10000 ports = slow startup | Tidak perlu |
| Configuration | Perlu local_net, external_* careful | Minimal |

---

## 3. Security (Host Mode)

### Firewall Configuration (UFW)

```bash
# Di server 172 (Debian host)
sudo ufw default deny incoming
sudo ufw default allow outgoing

# Essential ports
sudo ufw allow 22/tcp          # SSH
sudo ufw allow 5060/udp        # SIP signaling
sudo ufw allow 8089/tcp        # WebSocket for WebRTC
sudo ufw allow 10000:20000/udp # RTP media

# Enable
sudo ufw enable
sudo ufw status
```

### Fail2ban untuk SIP Attacks

```bash
sudo apt install fail2ban

# Create /etc/fail2ban/jail.d/asterisk.conf
[asterisk]
enabled = true
filter = asterisk
action = iptables-allports[name=asterisk, protocol=all]
logpath = /var/log/asterisk/messages
maxretry = 3
bantime = 86400
```

### PJSIP Security Settings

```ini
; Di pjsip_wizard.conf - sudah diterapkan
endpoint/auth = required          ; Wajib authentication
endpoint/context = internal       ; Isolate context
aor/max_contacts = 5              ; Limit registrations
```

### Checklist Keamanan

- [ ] UFW enabled di server 172
- [ ] Fail2ban configured untuk Asterisk
- [ ] SSH key-only (no password auth)
- [ ] Strong passwords untuk extensions (Maja1234 = dev only!)
- [ ] TLS/WSS untuk production (currently WS)

---

## 4. Extension Sync (171 → 172)

### Arsitektur Sync

```
┌─────────────────┐     SSH/SCP      ┌─────────────────┐
│  SERVER 171     │ ───────────────> │  SERVER 172     │
│  Laravel CMS    │                  │  Asterisk PBX   │
│  PostgreSQL     │                  │  Docker         │
└─────────────────┘                  └─────────────────┘
        │                                    │
        │ Extension.php                      │ pjsip_wizard.conf
        │ (Model Events)                     │
        ↓                                    ↓
   created() ─────> AsteriskService ─────> addExtension()
   updated() ─────> AsteriskService ─────> resync
   deleted() ─────> AsteriskService ─────> removeExtension()
```

### File yang Terlibat

| File | Lokasi | Fungsi |
|------|--------|--------|
| `Extension.php` | 171:/var/www/html/smartX/app/Models/ | Model dengan event hooks |
| `AsteriskService.php` | 171:/var/www/html/smartX/app/Services/ | SSH executor ke 172 |
| `pjsip_wizard.conf` | 172:/etc/asterisk/ (dalam Docker) | PJSIP endpoints config |

### Sync Flow

1. User create extension di CMS (171)
2. `Extension::created()` event triggered
3. `AsteriskService::addExtension()` dipanggil
4. SSH ke 172: append config ke `pjsip_wizard.conf`
5. `pjsip reload` executed

### Known Issue & Fix

**Problem:** Extension 6003-6023 tidak tersync karena dibuat SEBELUM auto-sync code di-deploy.

**Solution - Resync All:**
```bash
# Dari laptop, jalankan:
d:\...\larkon\resync_all_extensions.cmd
```

### Extension Type Templates

```ini
; WebRTC (6000-6009) - untuk Browser
[webrtc-template](!)
endpoint/webrtc = yes
endpoint/dtls_auto_generate_cert = yes
endpoint/media_encryption_optimistic = yes

; Softphone (6010-6023) - untuk Zoiper/Phoner
[softphone-template](!)
endpoint/webrtc = no
endpoint/media_encryption = no
endpoint/media_encryption_optimistic = no
```

---

## 5. Call Stability

### Browser ↔ Browser (WebRTC)

| Requirement | Setting | Status |
|-------------|---------|--------|
| WebSocket Transport | port 8089 | ✅ |
| DTLS/SRTP | auto_generate_cert | ✅ |
| ICE | disabled (Asterisk handles relay) | ✅ |
| Codec | opus, g722, ulaw, alaw | ✅ |

**Verified Working:** 6000 ↔ 6001, 6001 ↔ 6002

### Browser ↔ Softphone (Mixed)

| Requirement | Setting | Status |
|-------------|---------|--------|
| media_encryption_optimistic | yes (both sides) | ✅ |
| Different templates | WebRTC vs Softphone | ✅ |
| RTP bridging | Asterisk transcrypt | ✅ |

**Issue:** Softphone extensions (6010+) belum lengkap di Asterisk.

### Softphone ↔ Softphone

| Requirement | Setting | Status |
|-------------|---------|--------|
| UDP Transport | port 5060 | ✅ |
| No encryption | media_encryption=no | ✅ |
| NAT traversal | rtp_symmetric, force_rport | ✅ |

### Troubleshooting Checklist

| Symptom | Check | Solution |
|---------|-------|----------|
| No audio | `pjsip show channelstats` | Check RTP ports, firewall |
| One-way audio | NAT settings | Verify rtp_symmetric=yes |
| Call drops immediately | Encryption mismatch | Check template assignment |
| Registration fails | Credentials | Verify extension exists in pjsip |

### Useful Debug Commands

```bash
# Check registered endpoints
docker exec asterisk asterisk -rx 'pjsip show endpoints'

# Check active channels
docker exec asterisk asterisk -rx 'core show channels'

# Check RTP stats during call
docker exec asterisk asterisk -rx 'pjsip show channelstats'

# Verbose SIP debug
docker exec asterisk asterisk -rx 'pjsip set logger on'
```

---

## 6. Quick Reference

### Server IPs

| Server | IP | Role | SSH |
|--------|-----|------|-----|
| 171 | 103.154.80.171 | Laravel CMS + PostgreSQL | root@... |
| 172 | 103.154.80.172 | Asterisk PBX (Docker) | root@... |

### Extension Ranges

| Range | Template | Use Case |
|-------|----------|----------|
| 6000-6009 | WebRTC | Browser/Turret |
| 6010-6023 | Softphone | Zoiper/Phoner |
| 7000+ | Custom | Special cases |

### Default Credentials (DEV ONLY!)

| What | Value |
|------|-------|
| Extension Password | Maja1234 |
| CMS Admin | admin@smartx.local / admin123 |
| PostgreSQL | postgres / smartx2025 |

---

## 7. Next Actions

- [ ] Resync all extensions (6003-6023) ke Asterisk
- [ ] Enable UFW firewall di server 172
- [ ] Setup Fail2ban
- [ ] Change dev passwords sebelum production
- [ ] Upgrade WS → WSS (TLS) untuk production
