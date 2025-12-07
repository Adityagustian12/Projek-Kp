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
        
        // Try to use route storage.file first (more reliable on shared hosting)
        // This bypasses symlink issues
        try {
            if (\Route::has('storage.file')) {
                return route('storage.file', ['path' => $path]);
            }
        } catch (\Exception $e) {
            // Fallback to direct URL
        }
        
        // Fallback: Build URL directly
        $appUrl = rtrim(config('app.url', env('APP_URL', 'http://localhost')), '/');
        $url = $appUrl . '/storage/' . $path;
        
        // Fix any double slashes (but preserve http:// or https://)
        $url = preg_replace('#([^:])//+#', '$1/', $url);
        
        return $url;
    }
}

