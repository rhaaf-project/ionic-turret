<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AsteriskService
{
    private string $pbxHost = '103.154.80.172';

    /**
     * Execute SSH command on PBX host
     * Uses sudo because Laravel runs as www-data
     */
    private function sshExec(string $command): string
    {
        $sshCommand = "sudo ssh -o StrictHostKeyChecking=no -o BatchMode=yes root@{$this->pbxHost} \"{$command}\"";
        Log::info('[Asterisk] SSH: ' . $command);

        $output = shell_exec($sshCommand . ' 2>&1');
        Log::info('[Asterisk] Output: ' . substr($output ?? '', 0, 500));

        return $output ?? '';
    }

    /**
     * Execute command inside Asterisk docker container
     */
    public function executeCommand(string $command): string
    {
        return $this->sshExec("docker exec asterisk {$command}");
    }

    /**
     * Reload PJSIP config
     */
    public function reloadPjsip(): bool
    {
        $result = $this->executeCommand("asterisk -rx 'pjsip reload'");
        return str_contains($result, 'reloaded') || str_contains($result, 'Reload') || str_contains($result, 'Module');
    }

    /**
     * Add extension to pjsip.conf using docker cp approach
     * @param string $type 'webrtc' or 'softphone'
     */
    public function addExtension(string $extension, string $secret, string $name = '', string $type = 'webrtc'): bool
    {
        Log::info("[Asterisk] Adding extension {$extension} (type: {$type})");

        // Generate config based on type
        $config = ($type === 'softphone')
            ? $this->generateSoftphoneConfig($extension, $secret)
            : $this->generateWebrtcConfig($extension, $secret);

        // Step 1: Copy current pjsip.conf from container to host
        $this->sshExec("docker cp asterisk:/etc/asterisk/pjsip.conf /tmp/pjsip_current.conf");

        // Step 2: Create extension config on remote host
        $escapedConfig = str_replace("'", "'\\''", $config);
        $this->sshExec("echo '{$escapedConfig}' >> /tmp/pjsip_current.conf");

        // Step 3: Copy back to container
        $this->sshExec("docker cp /tmp/pjsip_current.conf asterisk:/etc/asterisk/pjsip.conf");

        // Step 4: Cleanup
        $this->sshExec("rm /tmp/pjsip_current.conf");

        // Step 5: Reload PJSIP
        return $this->reloadPjsip();
    }

    /**
     * Generate WebRTC config (for browsers)
     */
    private function generateWebrtcConfig(string $extension, string $secret): string
    {
        return "
[{$extension}]
type=endpoint
context=internal
disallow=all
allow=opus,g722,ulaw,alaw
auth={$extension}
aors={$extension}
webrtc=yes
dtls_auto_generate_cert=yes
rtp_symmetric=yes
force_rport=yes
rewrite_contact=yes
direct_media=no
media_encryption_optimistic=yes

[{$extension}]
type=auth
auth_type=userpass
username={$extension}
password={$secret}

[{$extension}]
type=aor
max_contacts=5
remove_existing=yes
";
    }

    /**
     * Generate Softphone config (for Zoiper/Phoner)
     */
    private function generateSoftphoneConfig(string $extension, string $secret): string
    {
        return "
[{$extension}]
type=endpoint
context=internal
disallow=all
allow=opus,g722,ulaw,alaw
auth={$extension}
aors={$extension}
webrtc=no
rtp_symmetric=yes
force_rport=yes
rewrite_contact=yes
direct_media=no
media_encryption=no
media_encryption_optimistic=no
ice_support=no
dtls_auto_generate_cert=no

[{$extension}]
type=auth
auth_type=userpass
username={$extension}
password={$secret}

[{$extension}]
type=aor
max_contacts=5
remove_existing=yes
";
    }

    /**
     * Remove extension from pjsip.conf
     */
    public function removeExtension(string $extension): bool
    {
        Log::info("[Asterisk] Removing extension {$extension}");

        // Copy out, remove blocks, copy back
        $this->sshExec("docker cp asterisk:/etc/asterisk/pjsip.conf /tmp/pjsip_current.conf");

        // Remove all 3 blocks (endpoint, auth, aor) for this extension
        $sedCmd = "sed -i '/^\\[{$extension}\\]/,/^$/d' /tmp/pjsip_current.conf";
        $this->sshExec($sedCmd);

        $this->sshExec("docker cp /tmp/pjsip_current.conf asterisk:/etc/asterisk/pjsip.conf");
        $this->sshExec("rm /tmp/pjsip_current.conf");

        return $this->reloadPjsip();
    }

    /**
     * Get list of registered endpoints
     */
    public function getEndpoints(): string
    {
        return $this->executeCommand("asterisk -rx 'pjsip show endpoints'");
    }
}
