<?php

class telegramEvent
{
    /*
    * TODO $params array('app','hook', 'params(params,keys,visible))
    */
    public function call($params)
    {
        if (waSystemConfig::isDebug()) {
            $debug = waConfig::get('telegram_hook_debug', array());
            $start = microtime(1);
            $event = wa('telegram')->event($params['app'].'.'.$params['hook'], $params['params']);
            $end = microtime(1);
            $debug[] = array (
                'id' => $params['app'].'.'.$params['hook'],
                'hook' => $end-$start,
                'total' => waSystemConfig::getTime(),
            );
            waConfig::set('telegram_hook_debug', $debug);
        } else {
            $event = wa('telegram')->event($params['app'].'.'.$params['hook'], $params['params']);
        }
        $result = array();
        foreach($event as $ev) {
            if ($ev) {
                foreach ((array)$ev as $key => $handler) {
                    if (array_key_exists($key, $result)) {
                        $result[$key] .= $handler;
                    } else {
                        $result[$key] = $handler;
                    }
                }
            }
        }
        return ifempty($result, null);
    }
}
