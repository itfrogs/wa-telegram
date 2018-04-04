<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 4/2/18
 * Time: 8:53 PM
 */
require_once wa()->getAppPath('','telegram') . '/lib/vendors/telegram-bot-sdk/autoload.php';

use Telegram\Bot\Api;

class telegramApi extends Api
{
    public function __construct(string $token = null, bool $async = false, $http_client_handler = null)
    {
        parent::__construct($token, $async, $http_client_handler);
    }

}