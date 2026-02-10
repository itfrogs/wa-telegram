<?php
class telegramBackendActions extends waViewActions
{
    public function preExecute()
    {
        if (!waRequest::isXMLHttpRequest()) {
            $this->setLayout(new telegramDefaultLayout());
        }
    }

    public function defaultAction()
    {
        $message = _w('Platform for creating Telegram bots.');
        $this->view->assign('message', $message);

        if (PHP_VERSION_ID >= 70400 && PHP_VERSION_ID < 80100) {
            $php_text = '<span style="color: darkorange">Версия PHP '.PHP_VERSION.' подходит для работы, но некоторые функции приложения могут не работать. Рекомендуется перейти на PHP > 8.1.0</span>';
        }
        elseif (PHP_VERSION_ID >= 80100) {
            $php_text = '<span style="color: green">Версия PHP '.PHP_VERSION.' подходит для работы приложения.</span> ';
        }
        else {
            $php_text = '<span style="color: red">Версия PHP '.PHP_VERSION.' не подходит для работы приложения.</span> ';
        }

        $this->view->assign('php_text', $php_text);
    }
}
