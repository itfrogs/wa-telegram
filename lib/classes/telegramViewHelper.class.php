<?php

class telegramViewHelper extends waAppViewHelper
{
    public static function generateHandlers()
    {
        $handlers = include_once(wa('telegram')->getAppPath('lib/config/handlers.php'));
        return $handlers;
    }

    public function __call($name, $arguments)
    {
        if (class_exists('telegram'.ucfirst($name).'PluginViewHelper')) {
            if (method_exists('telegram'.$name.'PluginViewHelper', 'execute')) {
                $class = 'telegram'.ucfirst($name).'PluginViewHelper';
                $class = new $class($this->wa());
                return call_user_func_array(array($class, 'execute'), $arguments);
            }
        }
        return false;
    }
}
