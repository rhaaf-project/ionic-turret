<?php
// Test script for AsteriskService
// Run with: php test_asterisk.php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\AsteriskService;

echo "Testing AsteriskService...\n";

$asterisk = new AsteriskService();

// Test 1: Execute simple command
echo "1. Testing hostname...\n";
$result = $asterisk->executeCommand('hostname');
echo "Result: $result\n";

// Test 2: Add test extension
echo "2. Testing addExtension(9999, TestPass)...\n";
$success = $asterisk->addExtension('9999', 'TestPass', 'TestExt');
echo "Success: " . ($success ? 'YES' : 'NO') . "\n";

// Test 3: Check if added
echo "3. Checking if 9999 in config...\n";
$endpoints = $asterisk->getEndpoints();
echo "Endpoints contain 9999: " . (str_contains($endpoints, '9999') ? 'YES' : 'NO') . "\n";

// Test 4: Remove test extension
echo "4. Removing test extension...\n";
$asterisk->removeExtension('9999');
echo "Done!\n";
