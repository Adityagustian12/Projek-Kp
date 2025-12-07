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
                $url = \Storage::disk('public')->url($path);
                // Fix double slashes
                $url = str_replace('://', '://', preg_replace('#([^:])//+#', '$1/', $url));
                return $url;
            }
        } catch (\Exception $e) {
            // Fallback to asset()
        }

        // Fallback to asset()
        $url = asset('storage/' . $path);
        // Fix double slashes
        return str_replace('://', '://', preg_replace('#([^:])//+#', '$1/', $url));
    }
}

