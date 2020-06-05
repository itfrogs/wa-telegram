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
//require_once wa()->getAppPath('','telegram') . '/lib/vendors/telegram-bot-sdk-3.0/autoload.php';
require_once wa()->getAppPath('','telegram') . '/lib/vendors/telegram-bot-sdk-2.0/autoload.php';

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

    /**
     * @param $endpoint
     * @param array $params
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function telegramGet($endpoint, array $params = []) {
        $this->get($endpoint, $params);
    }

    /**
     * Builds a custom keyboard markup.
     *
     * <code>
     * $params = [
     *   'keyboard'          => '',
     *   'resize_keyboard'   => '',
     *   'one_time_keyboard' => '',
     *   'selective'         => '',
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#replykeyboardmarkup
     *
     * @param array $params
     *
     * @var array   $params ['keyboard']
     * @var bool    $params ['resize_keyboard']
     * @var bool    $params ['one_time_keyboard']
     * @var bool    $params ['selective']
     *
     * @return string
     */
    public function replyKeyboardMarkup(array $params)
    {
        return json_encode($params);
    }

    /**
     * Hide the current custom keyboard and display the default letter-keyboard.
     *
     * <code>
     * $params = [
     *   'hide_keyboard' => true,
     *   'selective'     => false,
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#replykeyboardhide
     *
     * @param array $params
     *
     * @var bool    $params ['hide_keyboard']
     * @var bool    $params ['selective']
     *
     * @return string
     */
    public static function replyKeyboardHide(array $params = [])
    {
        return json_encode(array_merge(['hide_keyboard' => true, 'selective' => false], $params));
    }

    /**
     * Display a reply interface to the user (act as if the user has selected the bot‘s message and tapped ’Reply').
     *
     * <code>
     * $params = [
     *   'force_reply' => true,
     *   'selective'   => false,
     * ];
     * </code>
     *
     * @link https://core.telegram.org/bots/api#forcereply
     *
     * @param array $params
     *
     * @var bool    $params ['force_reply']
     * @var bool    $params ['selective']
     *
     * @return string
     */
    public static function forceReply(array $params = [])
    {
        return json_encode(array_merge(['force_reply' => true, 'selective' => false], $params));
    }
}