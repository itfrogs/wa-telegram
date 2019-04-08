<?php

/**
 * Class telegramConfig
 */
class telegramConfig extends waAppConfig
{
    /**
     * @var waAppSettingsModel
     */
    protected $asm;

    /**
     *
     */
    const ROWS_PER_PAGE = 30;

    /**
     * @var
     */
    protected static $max_execution_time;

    // see also a hack in FrontController->dispatch()

    /**
     * @param array $route
     * @return array|mixed|null
     */
    public function getRouting($route = array())
    {
        if ($this->routes === null) {
            $this->routes = $this->getRoutingRules();
        }
        return $this->routes;
    }

    /**
     * @param $type
     * @return string
     */
    protected function getRoutingPath($type)
    {
        if ($type === null) {
            $type = $this->getEnvironment();
        }
        $filename = ($type === 'backend') ? 'routing.backend.php' : 'routing.php';
        $path = $this->getConfigPath($filename, true, $this->application);
        if (!file_exists($path)) {
            $path = $this->getConfigPath($filename, false, $this->application);
        }
        return $path;
    }

    /**
     * @param array $route
     * @return array
     */
    protected function getRoutingRules($route = array())
    {
        $routes = array();
        if ($this->getEnvironment() === 'backend') {
            $path = $this->getRoutingPath('backend');
            if (file_exists($path)) {
                $routes = array_merge($routes, include($path));
            }
        }

        $path = $this->getRoutingPath('frontend');
        if (file_exists($path)) {
            $routes = array_merge($routes, include($path));
        }

        return array_merge($this->getPluginRoutes($route), $routes);
    }

    /**
     * @param null $default
     * @return int|null
     */
    public function getMaxExecutionTime($default = null)
    {
        if (self::$max_execution_time === null) {
            self::$max_execution_time = (int)ini_get('max_execution_time');
        }
        if ($default === null) {
            $default = wa()->getEnv() === 'cli' ? 600 : 120;
        }
        return self::$max_execution_time > 0 ? self::$max_execution_time : $default;
    }

    /**
     * @param $route
     * @return array
     */
    protected function getPluginRoutes($route)
    {
        /**
         * Extend routing via plugin routes
         * @event routing
         * @param array $routes
         * @return array $routes routes collected for every plugin
         */
        $result = wa()->event(array($this->application, 'routing'), $route);
        $all_plugins_routes = array();
        foreach ($result as $plugin_id => $routing_rules) {
            if (!$routing_rules) {
                continue;
            }
            $plugin = str_replace('-plugin', '', $plugin_id);
            foreach ($routing_rules as $url => & $route) {
                if (!is_array($route)) {
                    list($route_ar['module'], $route_ar['action']) = explode('/', $route);
                    $route = $route_ar;
                }
                if (!array_key_exists('plugin', $route)) {
                    $route['plugin'] = $plugin;
                }
                $all_plugins_routes[$url] = $route;
            }
            unset($route);
        }
        return $all_plugins_routes;
    }

    /**
     * @return waAppSettingsModel
     */
    protected function getSettingsModel()
    {
        return $this->asm !== null ? $this->asm : ($this->asm = new waAppSettingsModel());
    }

    /**
     * @return array|null
     */
    public function getPlugins()
    {
        $fix = $this->plugins === null;
        parent::getPlugins();
        if ($fix) {
            foreach ($this->plugins as $p_id => $p) {
                if (!empty($p['telegram_settings'])) {
                    $this->plugins[$p_id]['custom_settings_url'] = '?plugin='.$p['id'].'&module=backend&action=settings';
                }
            }
        }
        return $this->plugins;
    }
}
