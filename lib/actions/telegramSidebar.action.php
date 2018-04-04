<?php

class telegramSidebarAction extends waViewAction
{
    public function execute()
    {
        // Has to come before other assigns because it messes up with smarty vars
        $this->view->assign('backend_sidebar', $this->pluginHook());
        $this->setTemplate('templates/actions/Sidebar.html');
    }

    protected function pluginHook()
    {
        $event_params = array();
        return wa()->event('backend_sidebar', $event_params, array(
            'top_li', 'section', 'bottom_li',
        ));
    }
}
