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
    }
}
