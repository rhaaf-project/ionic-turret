<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AsteriskService
{
    private string $pbxHost = '103.154.80.172';

    /**
     * Execute SSH command on PBX host (NOT in docker yet)
     */
    private function sshExec(string $command): string
    {
        $sshCommand = "ssh -o StrictHostKeyChecking=no root@{$this->pbxHost} \"{$command}\"";
        Log::info('[Asterisk] SSH: ' . $command);

        $output = shell_exec($sshCommand . ' 2>&1');
        Log::info('[Asterisk] Output: ' . $output);

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
        return str_contains($result, 'reloaded') || str_contains($result, 'Reload');
    }

    /**
     * Add extension to pjsip_wizard.conf
     */
    public function addExtension(string $extension, string $secret, string $name = ''): bool
    {
        $config = "[{$extension}]
type = wizard
accepts_registrations = yes
sends_registrations = no
accepts_auth = yes
sends_auth = no
endpoint/context = internal
endpoint/allow = ulaw,alaw,opus
endpoint/webrtc = yes
endpoint/dtls_auto_generate_cert = yes
endpoint/rtp_symmetric = yes
endpoint/force_rport = yes
endpoint/rewrite_contact = yes
endpoint/direct_media = no
inbound_auth/username = {$extension}
inbound_auth/password = {$secret}
";

        // Write config to local temp file
        $tempFile = '/tmp/ext_' . $extension . '.conf';
        file_put_contents($tempFile, $config);

        // SCP to host 172
        $scpCmd = "scp -o StrictHostKeyChecking=no {$tempFile} root@{$this->pbxHost}:/tmp/";
        shell_exec($scpCmd . ' 2>&1');

        // Copy from host into docker container
        $this->sshExec("docker cp /tmp/ext_{$extension}.conf asterisk:/tmp/");

        // Append to pjsip_wizard.conf inside container
        $this->executeCommand("sh -c 'cat /tmp/ext_{$extension}.conf >> /etc/asterisk/pjsip_wizard.conf'");

        // Cleanup
        @unlink($tempFile);
        $this->sshExec("rm /tmp/ext_{$extension}.conf");
        $this->executeCommand("rm /tmp/ext_{$extension}.conf");

        return $this->reloadPjsip();
    }

    /**
     * Remove extension from pjsip_wizard.conf
     */
    public function removeExtension(string $extension): bool
    {
        // Use sed to remove extension block (from [ext] to next empty line)
        $this->sshExec("docker exec asterisk sed -i '/^\\[{$extension}\\]/,/^\$/d' /etc/asterisk/pjsip_wizard.conf");

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
