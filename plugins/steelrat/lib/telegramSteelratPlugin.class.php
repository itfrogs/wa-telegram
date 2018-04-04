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
        $telegramUrl = wa('telegram')->getRouteUrl('telegram/frontend', array(), true);
        $view->assign('plugin_url', $telegramUrl . 'steelrat/bot');
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
        $telegramUrl = wa('telegram')->getRouteUrl('telegram/frontend', array(), true);
        $view->assign('plugin_url', $telegramUrl . 'steelrat/bot');
        $view->assign('settings', $plugin->getSettings());
        return $view->fetch($plugin->getPluginPath() . '/templates/controls/webhookUrlControl.html');
    }
}
