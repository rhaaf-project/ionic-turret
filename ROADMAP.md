# SmartUCX Turret - Development Roadmap

## Phase 1: Core Functionality (Browser) ✅
*Status: Almost Complete*

- [x] SIP Calls working
- [x] Windows-style Tab System  
- [x] Group Talk configuration
- [x] Audio Recording to channels
- [x] Virtual Keyboard
- [ ] Fix Laravel API login (server 171 credentials)

---

## Phase 2: CMS Integration
*Status: In Progress*

- [ ] Fix Laravel server 171 (SSH check, reset credentials)
- [ ] SSH setup Laravel 171 → Asterisk 172
- [ ] Extension CRUD from CMS
- [ ] User management (login tied to extensions)
- [ ] Sync extensions DB ↔ Asterisk pjsip.conf

---

## Phase 3: Multi-Device Demo 🎯
*Milestone: Demo to Boss*

- [ ] Test on 2+ different devices simultaneously
- [ ] Verify SIP calls between devices
- [ ] Test channel assignment per device
- [ ] Verify Group Talk across devices
- [ ] Document demo script/steps

---

## Phase 4: Electron Integration
*After core stable*

- [ ] WhatsApp webview integration
- [ ] Teams webview integration
- [ ] Touch event configuration
- [ ] PTT hotkey setup
- [ ] Window management (minimize, maximize)

---

## Phase 5: Production Hardening
*Before deployment*

- [ ] Migrate SSH → ARI for Asterisk control
- [ ] HTTPS/TLS everywhere
- [ ] Audit logging
- [ ] Error handling & recovery
- [ ] Performance optimization
- [ ] Security review

---

## Server Architecture

| Server | IP | Role |
|--------|-----|------|
| 171 | 103.154.80.171 | Laravel CMS + PostgreSQL |
| 172 | 103.154.80.172 | Asterisk PBX (Docker) |

## Key Credentials (Dev)

- Laravel Admin: `admin@smartx.local` / `admin123`
- Asterisk Extensions: 6000-6020 / `Maja1234`
- SIP WebSocket: `wss://103.154.80.172:8089/ws`
