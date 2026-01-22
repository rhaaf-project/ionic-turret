# Post-Mortem: Cloning Laravel CMS to Subdirectory (103.154.80.171)

**Agent Model**: Antigravity (Powered by Gemini 3 Pro - High)
**Date**: 2026-01-23

## Objective
Clone the existing Laravel application (`smartX`) running at `http://103.154.80.171/` (root) to a subdirectory `http://103.154.80.171/SmartSBC`, ensuring complete isolation of the codebase but preserving the database connection.

## Summary of Issues
The process encountered persistent **404 Not Found** errors for the clone and a temporary **Regression (Down)** of the original site.

### 1. Nginx Alias vs. Laravel Path Detection (The "Laravel 404")
*   **Method**: Used Nginx `alias /var/www/html/SmartSBC/public` inside `location /SmartSBC`.
*   **Result**: Laravel booted (verified by 404 page style) but could not match routes.
*   **Cause**: Nginx does not automatically strip the alias prefix (`/SmartSBC`) from the `REQUEST_URI` passed to PHP.
    *   Nginx passed `REQUEST_URI: /SmartSBC/admin`
    *   Laravel expected `REQUEST_URI: /admin`
*   **Attempted Fix**: Manually forcing `fastcgi_param SCRIPT_NAME /SmartSBC/index.php`. This worked for routes but broke direct file access.

### 2. Nginx Nested Location Bug (The "File not found" / "Primary script unknown")
*   **Method**: Nested `location ~ \.php$` block inside `location /SmartSBC` with `alias`.
*   **Result**: Accessing direct files like `debug_laravel.php` returned text "File not found."
*   **Cause**: In Nginx, when `alias` is used, `$request_filename` calculation in nested locations is notoriously widely misunderstood and often resolves incorrectly (e.g., repeating the path or missing the root).

### 3. Server Name Wildcard Conflict (The "Regression")
*   **Method**: Created a new config `laravel` with `server_name _;`.
*   **Result**: The original site `http://103.154.80.171/admin` went down (404).
*   **Cause**: The new configuration file loaded alphabetically or by priority and "hijacked" the execution for the IP address. Since it didn't include the specific rules for the original site (which uses specific `$realpath_root` logic), the original site failed.

## The Final Solution: "Symlink Repatriation"

To eliminate Nginx "Alias" complexity entirely, we switched to a filesystem-based approach.

1.  **Renaming**: Moved the clone to `/var/www/html/SmartSBC_App`.
2.  **Symlinking**: Created a symlink at `/var/www/html/smartX/public/SmartSBC` pointing to `SmartSBC_App/public`.
    *   *Effect*: Nginx sees `/SmartSBC` as just another folder in the main root. No `alias` directive is needed.
3.  **Merged Config**: Created `smartx_with_sbc.conf` that:
    *   Restores the exact original configuration for `/`.
    *   Adds a simple `location /SmartSBC` block to handle the `try_files` logic for the clone.
    *   Listens explicitly on `103.154.80.171` to ensure priority.

## Verification
*   **Original**: `curl -I http://103.154.80.171/admin` -> **302 Found** (Login)
*   **Clone**: `curl -I http://103.154.80.171/SmartSBC/admin` -> **302 Found** (Login)
*   **Static**: `curl -I http://103.154.80.171/SmartSBC/test.html` -> **200 OK**
