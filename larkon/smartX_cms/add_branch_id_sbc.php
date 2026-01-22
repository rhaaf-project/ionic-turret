<?php
// Add branch_id column to sbcs table and create sample data

require '/var/www/html/smartX/vendor/autoload.php';
$app = require_once '/var/www/html/smartX/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Branch;
use App\Models\Sbc;

// Add branch_id column if not exists
if (!Schema::hasColumn('sbcs', 'branch_id')) {
    Schema::table('sbcs', function (Blueprint $table) {
        $table->unsignedBigInteger('branch_id')->nullable()->after('call_server_id');
    });
    echo "Added branch_id column to sbcs table\n";
} else {
    echo "branch_id column already exists\n";
}

// Get branches
$branches = Branch::all();
echo "Found " . $branches->count() . " branches\n";

// Assign SBCs to some branches
$sbcs = Sbc::all();
if ($sbcs->count() > 0 && $branches->count() > 0) {
    // Assign first SBC to first branch
    $sbc1 = $sbcs->first();
    $branch1 = $branches->first();
    $sbc1->branch_id = $branch1->id;
    $sbc1->save();
    echo "Assigned SBC '{$sbc1->name}' to Branch '{$branch1->name}' (ID: {$branch1->id})\n";

    // If there's a second SBC, assign to second branch
    if ($sbcs->count() > 1 && $branches->count() > 1) {
        $sbc2 = $sbcs->skip(1)->first();
        $branch2 = $branches->skip(1)->first();
        $sbc2->branch_id = $branch2->id;
        $sbc2->save();
        echo "Assigned SBC '{$sbc2->name}' to Branch '{$branch2->name}' (ID: {$branch2->id})\n";
    }
}

echo "\nDone! Now refresh the topology page.\n";
