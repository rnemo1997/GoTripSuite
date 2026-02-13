<?php

if (!function_exists('localizedUrl')) {
    /**
     * Generate the current URL in a different locale.
     */
    function localizedUrl(string $locale): string
    {
        $route = request()->route();

        if (!$route || !$route->getName()) {
            // Fallback: just prepend locale prefix to current path
            $path = request()->getPathInfo();

            // Strip existing locale prefix
            $path = preg_replace('#^/(nl|de|fr|es)(/|$)#', '/', $path);

            if ($locale === 'en') {
                return url($path);
            }

            return url('/' . $locale . ($path === '/' ? '' : $path));
        }

        $params = request()->route()->parameters();
        $params['locale'] = $locale === 'en' ? null : $locale;

        // Preserve query string
        $query = request()->query();

        $url = route($route->getName(), $params);

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }
}
