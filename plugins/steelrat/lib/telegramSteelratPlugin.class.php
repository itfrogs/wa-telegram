<?php

/**
 * Class telegramSteelratPlugin
 */
class telegramSteelratPlugin extends telegramPlugin
{
    /**
     * @var waView $view
     */
    private static $view;

    /**
     * @return waSmarty3View|waView
     * @throws waException
     */
    private static function getView()
    {
        if (!isset(self::$view)) {
            self::$view = waSystem::getInstance()->getView();
        }
        return self::$view;
    }

    /**
     * @var telegramSteelratPlugin $plugin
     */
    private static $plugin;

    /**
     * @return telegramSteelratPlugin|waPlugin
     * @throws waException
     */
    private static function getPlugin()
    {
        if (!isset(self::$plugin)) {
            self::$plugin = wa('telegram')->getPlugin('steelrat');
        }
        return self::$plugin;
    }

    /**
     * @return string
     * @throws waException
     */
    public static function getPluginPath()
    {
        $plugin = self::getPlugin();
        return $plugin->path;
    }

    /**
     * @return string
     * @throws waException
     */
    public static function getFeedbackControl()
    {
        $view = self::getView();
        $plugin = self::getPlugin();
        return $view->fetch($plugin->getPluginPath() . '/templates/controls/feedbackControl.html');
    }

    /**
     * @return string
     * @throws waException
     */
    public static function getBotUrlControl()
    {
        $view = self::getView();
        $plugin = self::getPlugin();
        $settlements = self::getSettlements();
        $view->assign('settlements', $settlements);

        return $view->fetch($plugin->getPluginPath() . '/templates/controls/botUrlControl.html');
    }

    /**
     * @return string
     * @throws waException
     */
    public static function getWebhookUrlControl()
    {
        $view = self::getView();
        $plugin = self::getPlugin();
        $settlements = self::getSettlements();
        $view->assign('settlements', $settlements);
        $view->assign('settings', $plugin->getSettings());
        return $view->fetch($plugin->getPluginPath() . '/templates/controls/webhookUrlControl.html');
    }

    /**
     * @return string
     * @throws waException
     */
    public static function getHttpsCheckControl()
    {
        $view = self::getView();
        $plugin = self::getPlugin();
        $view->assign('is_https', waRequest::isHttps());
        return $view->fetch($plugin->getPluginPath() . '/templates/controls/httpsCheckControl.html');
    }


    /**
     * @return array
     * @throws waException
     */
    public static function getSettlements()
    {
        $settlements = array();
        $routing = wa()->getRouting();
        $domain_routes = $routing->getByApp('telegram');
        foreach ($domain_routes as $domain => $routes) {
            foreach ($routes as $route) {
                $routing->setRoute($route, $domain);
                $settlement = wa('telegram')->getRouteUrl('telegram/frontend', array(), true);
                $settlements[] = $settlement;
            }
        }
        return $settlements;
    }
}
