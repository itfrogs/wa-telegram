<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 4/2/18
 * Time: 8:53 PM
 */

/*
 * Подключаем вендора https://github.com/irazasyed/telegram-bot-sdk
 */
require_once wa()->getAppPath('','telegram') . '/lib/vendors/telegram-bot-sdk/autoload.php';

use Telegram\Bot\Api;

class telegramApi extends Api
{
    public function __construct($token = null, $async = false, $http_client_handler = null)
    {
        parent::__construct($token, $async, $http_client_handler);
    }

    /**
     * @param $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }


    /**
     * @param $endpoint
     * @param array $params
     * @param bool $fileUpload
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function telegramPost($endpoint, array $params = [], $fileUpload = false) {
        $this->post($endpoint, $params, $fileUpload);
    }
}