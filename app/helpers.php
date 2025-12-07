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
        
        // Get APP_URL and ensure no trailing slash
        $appUrl = rtrim(config('app.url', env('APP_URL', 'http://localhost')), '/');
        
        // Build URL directly - more reliable
        $url = $appUrl . '/storage/' . $path;
        
        // Fix any double slashes (but preserve http:// or https://)
        $url = preg_replace('#([^:])//+#', '$1/', $url);
        
        return $url;
    }
}

