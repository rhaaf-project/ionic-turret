<?php

namespace App\Models;

use App\Services\AsteriskService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Extension extends Model
{
    use HasFactory;

    protected $fillable = [
        'extension',
        'name',
        'secret',
        'context',
        'is_active',
        'user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model and add Asterisk sync events
     */
    protected static function boot()
    {
        parent::boot();

        // Sync to Asterisk on create
        static::created(function (Extension $ext) {
            if ($ext->is_active) {
                try {
                    $asterisk = app(AsteriskService::class);
                    $asterisk->addExtension($ext->extension, $ext->secret, $ext->name);
                    Log::info("[Extension] Synced to Asterisk: {$ext->extension}");
                } catch (\Exception $e) {
                    Log::warning("[Extension] Failed to sync to Asterisk: " . $e->getMessage());
                }
            }
        });

        // Resync to Asterisk on update
        static::updated(function (Extension $ext) {
            try {
                $asterisk = app(AsteriskService::class);

                // If extension was deactivated, remove from Asterisk
                if (!$ext->is_active && $ext->wasChanged('is_active')) {
                    $asterisk->removeExtension($ext->extension);
                    Log::info("[Extension] Removed from Asterisk (deactivated): {$ext->extension}");
                }
                // If extension or secret changed, resync
                elseif ($ext->wasChanged(['extension', 'secret'])) {
                    $original = $ext->getOriginal('extension');
                    $asterisk->removeExtension($original);
                    $asterisk->addExtension($ext->extension, $ext->secret, $ext->name);
                    Log::info("[Extension] Resynced to Asterisk: {$ext->extension}");
                }
            } catch (\Exception $e) {
                Log::warning("[Extension] Failed to resync to Asterisk: " . $e->getMessage());
            }
        });

        // Remove from Asterisk on delete
        static::deleted(function (Extension $ext) {
            try {
                $asterisk = app(AsteriskService::class);
                $asterisk->removeExtension($ext->extension);
                Log::info("[Extension] Removed from Asterisk: {$ext->extension}");
            } catch (\Exception $e) {
                Log::warning("[Extension] Failed to remove from Asterisk: " . $e->getMessage());
            }
        });
    }

    /**
     * Get the user that owns this extension
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
