<?php
class appsRightConfig extends waRightConfig
{
    public function init()
    {   /**
     * @event rights.config
     * @param waRightConfig $this Rights setup object
     * @return void
     */
        wa()->event('rights.config', $this);
    }
}