<?php

class telegramPluginsActions extends waPluginsActions
{
    protected $plugins_hash = '#';
    protected $is_ajax = false;
    protected $shadowed = false;

    public function defaultAction()
    {
        if (!$this->getUser()->isAdmin($this->getApp())) {
            throw new waRightsException(_w('Access denied'));
        }
        $this->getResponse()->setTitle(_w('Plugin settings page'));

        if (!waRequest::isXMLHttpRequest()) {
            $this->setLayout(new telegramDefaultLayout());
        }
        parent::defaultAction();
    }
}
