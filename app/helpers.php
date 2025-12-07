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
        
        // Build URL directly - route /files/{path} will handle it
        // Using /files/ instead of /storage/ to bypass web server blocking
        $appUrl = rtrim(config('app.url', env('APP_URL', 'http://localhost')), '/');
        $url = $appUrl . '/files/' . $path;
        
        // Fix any double slashes (but preserve http:// or https://)
        $url = preg_replace('#([^:])//+#', '$1/', $url);
        
        return $url;
    }
}

