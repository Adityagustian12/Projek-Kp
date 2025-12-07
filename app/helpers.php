<?php

if (!function_exists('storage_url')) {
    /**
     * Get storage URL - more reliable than asset() for shared hosting
     * 
     * @param string $path Path relative to storage/app/public
     * @return string
     */
    function storage_url($path)
    {
        if (empty($path)) {
            return null;
        }

        // Normalize path
        $path = ltrim($path, '/');
        
        // Try Storage::url() first (most reliable)
        try {
            if (\Storage::disk('public')->exists($path)) {
                return \Storage::disk('public')->url($path);
            }
        } catch (\Exception $e) {
            // Fallback to asset()
        }

        // Fallback to asset()
        return asset('storage/' . $path);
    }
}

