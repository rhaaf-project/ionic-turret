## 1. SBC Clickable di Topology Map

### Status: ✅ DONE
- **Fix:** Updated `InterconnectivityResource.php` to pass `sbc_id` to topology data.
- **Verified:** Browser test confirmed clicking node with SBC navigates to Edit SBC page.
- **Commit:** `1962d9f`

---

## 2. CI/CD Pipeline Error - Ionic Turret

### Goal:
Fix GitHub Actions pipeline untuk deploy Ionic Turret ke Server 170

### Current Status:
- ✅ Local build sukses (`npm run build -- --configuration production`)
- ❌ GitHub Actions build **failed dengan exit code 3**

### Repository:
- **GitHub:** `rhaaf-project/ionic-turret`
- **Actions URL:** https://github.com/rhaaf-project/ionic-turret/actions

### Workflow File:
- **Path:** `.github/workflows/deploy-turret.yml`
- **Node Version:** 20
- **Build Command:** `npm run build -- --configuration production`
- **Output Path:** `www/*`

### Kemungkinan Masalah:
1. GitHub Actions runner memory terbatas
2. Angular budget exceeded di CI environment
3. Missing dependency di CI

### Yang perlu dilakukan:
1. Login GitHub → buka Actions → klik failed workflow
2. Klik "Build Turret" step → lihat detail error
3. Kemungkinan fix:
   - Naikkan Angular budget di `angular.json`
   - Atau set `CI=false` di workflow untuk skip warnings as errors

---

## Server Info:
- **IP:** `103.154.80.171`
- **Path:** `/var/www/html/smartX`
- **SSH:** `ssh root@103.154.80.171`
