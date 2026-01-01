<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use App\Services\AsteriskService;
use Illuminate\Http\Request;

class ExtensionController extends Controller
{
    protected AsteriskService $asterisk;

    public function __construct(AsteriskService $asterisk)
    {
        $this->asterisk = $asterisk;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Extension::all());
    }

    /**
     * Store a newly created resource in storage.
     * Also syncs to Asterisk PJSIP config
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'extension' => 'required|string|unique:extensions,extension',
            'name' => 'required|string|max:100',
            'secret' => 'required|string|max:50',
            'context' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        // Create in database
        $extension = Extension::create($validated);

        // Sync to Asterisk PBX
        if ($extension->is_active !== false) {
            $synced = $this->asterisk->addExtension(
                $extension->extension,
                $extension->secret,
                $extension->name
            );

            if (!$synced) {
                // Log warning but don't fail - DB is source of truth
                \Log::warning('[Extension] Failed to sync to Asterisk: ' . $extension->extension);
            }
        }

        return response()->json($extension, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $extension = Extension::findOrFail($id);
        return response()->json($extension);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $extension = Extension::findOrFail($id);

        $validated = $request->validate([
            'extension' => 'sometimes|string|unique:extensions,extension,' . $id,
            'name' => 'sometimes|string|max:100',
            'secret' => 'sometimes|string|max:50',
            'context' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $oldExtension = $extension->extension;
        $extension->update($validated);

        // If extension number changed or secret changed, resync to Asterisk
        if (isset($validated['extension']) || isset($validated['secret'])) {
            $this->asterisk->removeExtension($oldExtension);
            $this->asterisk->addExtension(
                $extension->extension,
                $extension->secret,
                $extension->name
            );
        }

        return response()->json($extension);
    }

    /**
     * Remove the specified resource from storage.
     * Also removes from Asterisk PJSIP config
     */
    public function destroy(string $id)
    {
        $extension = Extension::findOrFail($id);

        // Remove from Asterisk first
        $this->asterisk->removeExtension($extension->extension);

        // Then delete from database
        $extension->delete();

        return response()->json(null, 204);
    }
}
