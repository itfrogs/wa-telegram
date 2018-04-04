<?php
class telegramSettingsGeneralAction extends waViewAction
{
    public function preExecute()
    {
        if (!waRequest::isXMLHttpRequest()) {
            $this->setLayout(new telegramSettingsLayout());
        }
    }

    public function execute()
    {
        $message = 'Hello world!';
        $this->view->assign('message', $message);
    }
}
