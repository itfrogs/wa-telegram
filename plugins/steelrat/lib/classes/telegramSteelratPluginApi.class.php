<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 6/2/18
 * Time: 9:05 PM
 */

use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\TelegramClient;
use Telegram\Bot\FileUpload\InputFile;

/**
 * Class telegramSteelratPluginApi
 */
class telegramSteelratPluginApi extends telegramApi
{
    /**
     * @var
     */
    private static $settings;

    /**
     * @var
     */
    private $telegram_user_model;

    /**
     * @var
     */
    private $model;

    /**
     * @var telegramSteelratPlugin $plugin
     */
    private $plugin;

    /**
     * @var waView $view
     */
    private $view;

    /**
     * @var null
     */
    private $locale_path = null;

    /**
     * @var \Telegram\Bot\Api|null
     */
    public $mybot = null;

    /**
     * telegramSteelratPluginFrontendBotController constructor.
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     * @throws waException
     */
    public function __construct()
    {
        $this->plugin = wa('telegram')->getPlugin('steelrat');
        self::$settings = $this->plugin->getSettings();

        foreach (self::$settings as $key => $s) {
            if (!is_array($s) && stripos($key, '_title')) {
                self::$settings[$key] = base64_decode($s);
            }
        }

        $this->view = waSystem::getInstance()->getView();
        $this->telegram_user_model = new telegramSteelratPluginUserModel();
        $this->model = new waModel();

        $options = array(
            'headers' => [
                'User-Agent' => 'Telegram Bot PHP SDK v' . telegramApi::VERSION . ' - (https://github.com/irazasyed/telegram-bot-sdk)',
            ],
        );

        if (self::$settings['use_socks5']) {
            $proxy = 'socks5://';
            if (!empty(self::$settings['socks5_user']) && !empty(self::$settings['socks5_password'])) {
                $proxy .= self::$settings['socks5_user'] . ':' . self::$settings['socks5_password'] . '@';
            } elseif (!empty(self::$settings['socks5_user']) && empty(self::$settings['socks5_password'])) {
                $proxy .= self::$settings['socks5_user'] . '@';
            }

            if (!empty(self::$settings['socks5_address']) && !empty(self::$settings['socks5_port'])) {
                $proxy .= self::$settings['socks5_address'] . ':' . self::$settings['socks5_port'];
            } else {
                unset($proxy);
            }

            if (isset($proxy)) {
                $options['curl'] = array(
                    CURLOPT_PROXY => $proxy,
                    CURLOPT_HTTPPROXYTUNNEL => 1,
                    CURLOPT_HEADER => false,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_POST => 1,
                    CURLOPT_SSL_VERIFYPEER => false,

                );
            }
        }

        try {
            $httpClientHandler = $this->getGuzzleClientHandler($options);
        }
        catch (Exception $exception) {
            if (waSystemConfig::isDebug()) {
                waLog::dump($exception->getMessage(), 'telegram/steelrat-get-http-client.log');
            }
        }

        $this->setClient(new TelegramClient($httpClientHandler));

        $config = [
            'bots' => [
                'mybot' => [
                    'token' => self::$settings['key'],
                ],
            ]
        ];
        $bots = $this->getBot($config);
        $this->mybot = $bots->bot('mybot');

        parent::__construct(self::$settings['key'], false, $httpClientHandler);
    }

    public function getBotCommands() {
        $commands = $this->mybot->getMyCommands();
        return $commands;
    }

    public function setBotCommands($params) {
        $response = $this->mybot->setMyCommands($params);
        return $response;
    }

    /**
     * @return array
     */
    public static function getActionsArray() {
        $rows = 5;
        $columns = 3;

        $actions = array();

        /*
        for ($row = 1; $row <= $rows; $row++) {
            for ($column = 1; $column <= $columns; $column++) {
                $button_type = self::$settings['button_' . $row . '_' . $column . '_type'];
                if ($button_type != 'off') {
                    $actions[self::$settings['button_' . $row . '_' . $column . '_action']] = array(
                        'type'      => $button_type,
                        'title'     => self::$settings['button_' . $row . '_' . $column . '_title'],
                        'content'   => self::$settings['button_' . $row . '_' . $column . '_content'],
                        'path'      => self::$settings['button_' . $row . '_' . $column . '_path'],
                    );
                }
            }
        }

        */
        return $actions;
    }

    /**
     * @return array
     */
    public function getActions() {
        return telegramSteelratPluginApi::getActionsArray();
    }

    /**
     * @return array
     */
    public static function getButtonsArray() {
        $rows = 5;
        $columns = 3;

        $actions = self::getActionsArray();
        $buttons = array();

        /*
        for ($row = 1; $row <= $rows; $row++) {
            $buttons[$row - 1] = array();
            for ($column = 1; $column <= $columns; $column++) {
                $button_type = self::$settings['button_' . $row . '_' . $column . '_type'];
                $button_action = self::$settings['button_' . $row . '_' . $column . '_action'];
                if ($button_type != 'off' && isset($actions[$button_action])) {
                    $button = array();
                    if ($button_type == 'link') {
                        $button = array(
                            [
                                'text' => self::$settings['button_' . $row . '_' . $column . '_title'],
                                'url' => self::$settings['button_' . $row . '_' . $column . '_content'],
                            ]
                        );
                    }
                    else {
                        $button = array(
                            [
                                'text' => self::$settings['button_' . $row . '_' . $column . '_title'],
                                'callback_data' => $button_action,
                            ]
                        );
                    }

                    if (!empty($button)) {
                        $buttons[$row - 1] = array_merge($buttons[$row - 1], $button);
                    }
                }
            }
            if (empty($buttons[$row - 1])) unset($buttons[$row - 1]);
        }
        */

        return $buttons;
    }

    /**
     * @param $params
     * @throws waException
     */
    public function botSendMessage($params)
    {
        if ($params['chat_id'] < 0 || $params['is_group']) {
            try {
                $this->sendMessage($params);
            } catch (TelegramSDKException $e) {
                if (waSystemConfig::isDebug()) {
                    waLog::dump($params, 'telegram/steelrat-exception.log');
                    waLog::dump($e->getMessage(), 'telegram/steelrat-exception.log');
                }
            }
        }
        else {
            $user = $this->telegram_user_model->getById($params['chat_id']);

            if (!$user['blocked']) {
                try {
                    $this->sendMessage($params);
                } catch (Exception $e) {
                    if ($e->getMessage() && $e->getMessage() == 'Forbidden: bot was blocked by the user') {
                        //$this->botStop($params);
                        $user['blocked'] = 1;
                        $user = $this->telegram_user_model->updateById($user['chat_id'], $user);
                        if (waSystemConfig::isDebug()) {
                            waLog::log('Пользователь ' . !empty($user['name']) ? $user['name'] : $user['chat_id'] . ' забанил бота и был отписан.',
                                'telegram/steelrat-ban.log');
                        }
                    } else {
                        if (waSystemConfig::isDebug()) {
                            waLog::dump($params, 'telegram/steelrat-exception.log');
                            waLog::dump($e->getMessage(), 'telegram/steelrat-exception.log');
                        }
                    }
                }
            }
        }


    }

    /**
     * @throws waException
     */
    public function botSendPhoto($params) {
        $user = $this->telegram_user_model->getById($params['chat_id']);
        $path = $params['action']['path'];
        $file = InputFile::create($path);
        $this->sendPhoto([ 'chat_id' => $params['chat_id'], 'photo'=> $file, 'caption' => $params['action']['content'] ]);
    }

    /**
     * @throws waException
     */
    public function botSendDocument($params) {
        $user = $this->telegram_user_model->getById($params['chat_id']);
        $path = $params['action']['path'];
        $file = InputFile::create($path);
        $this->sendDocument([ 'chat_id' => $params['chat_id'], 'document'=> $file, 'reply_markup' => $this->getReplyMarkup($params) ]);
    }

    /**
     * @throws waException
     */
    public function botSendAudio($params) {
        $user = $this->telegram_user_model->getById($params['chat_id']);
        $path = $params['action']['path'];
        $file = InputFile::create($path);
        $this->sendAudio([ 'chat_id' => $params['chat_id'], 'audio'=> $file, 'title' => $params['action']['content'], 'reply_markup' => $this->getReplyMarkup($params) ]);
    }

    /**
     * @throws waException
     */
    public function botSendVideo($params) {
        $user = $this->telegram_user_model->getById($params['chat_id']);
        $path = $params['action']['path'];
        $file = InputFile::create($path);
        $this->sendVideo([ 'chat_id' => $params['chat_id'], 'video'=> $file, 'caption' => $params['action']['content'], 'reply_markup' => $this->getReplyMarkup($params) ]);
    }

    /**
     * @throws waException
     */
    public function botSendVoice($params) {
        $user = $this->telegram_user_model->getById($params['chat_id']);
        $path = $params['action']['path'];
        $file = InputFile::create($path);
        $this->sendVoice([ 'chat_id' => $params['chat_id'], 'voice'=> $file, 'reply_markup' => $this->getReplyMarkup($params)  ]);
    }

    /**
     * @param $params
     * @throws waException
     */
    public function botStart($params)
    {
        $user = $this->checkUser($params);
        if ($user['blocked'] == 1) {
            $user['blocked'] = 0;
            $user = $this->telegram_user_model->updateById($user['chat_id'], $user);
        }

        if (!empty($params['chat_username'])) {
            $group = $this->checkGroup($params);
        }

        $this->view->assign('user', $user);
        $text = self::$settings['welcome'];
        $reply_markup = $this->replyKeyboardHide();
        $send_params = [
            'chat_id' => $params['chat_id'],
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $reply_markup,
            'is_group' => $params['is_group'],
        ];

        if (!empty($group)) {
            $send_params['chat_id'] = '-' . $group['group_id'];
            $send_params['is_group'] = 1;
        }

        $this->botSendMessage($send_params);

        $params = [
            'commands' => [
                new Telegram\Bot\Objects\BotCommand([
                    'command' => 'start',
                    'description' => 'Запуск бота',
                ]),
            ]
        ];

        try {
            $response = $this->setBotCommands($params);
        } catch (TelegramSDKException $exception) {
            if (waSystemConfig::isDebug()) {
                waLog::dump($exception->getMessage(), 'telegram/steelrat-setcommands-error.log');
            }
        }
    }

    public function subscribeOrders($params) {
        $user = $this->checkUser($params);
        if ($user['blocked'] == 1) {
            $user['blocked'] = 0;
            $user = $this->telegram_user_model->updateById($user['chat_id'], $user);
        }

        if (!empty($params['chat_username'])) {
            $group = $this->checkGroup($params);
        }

        $text = 'Вы подписались на заказы';
        //$reply_markup = $this->getReplyMarkup($params);
        $reply_markup = $this->replyKeyboardHide();
        $send_params = [
            'chat_id' => $params['chat_id'],
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $reply_markup,
            'is_group' => $params['is_group'],
        ];

        if (!empty($group)) {
            $send_params['chat_id'] = '-' . $group['group_id'];
            $send_params['is_group'] = 1;

            $subscription = [
                'group_id'  => $group['group_id'],
                'subscription'  => 'orders',
            ];

            $subscription_row = $this->telegram_group_subscriptions_model->getByField($subscription);
            if (empty($subscription_row)) {
                $this->telegram_group_subscriptions_model->insert($subscription);
            }
            else {
                $send_params['text'] = 'Вы уже подписаны на заказы';            }
        }
        else {
            if (!empty(self::$settings['group_user_name']) && self::$settings['group_user_name'] != $params['chat_username']) {
                $send_params['text'] = 'Вы не имеете прав на подписку в этой группе';
            }
        }

        $this->botSendMessage($send_params);
    }

    public function unsubscribeOrders($params) {
        $user = $this->checkUser($params);
        if ($user['blocked'] == 1) {
            $user['blocked'] = 0;
            $user = $this->telegram_user_model->updateById($user['chat_id'], $user);
        }

        if (!empty($params['chat_username'])) {
            $group = $this->checkGroup($params);
        }

        $text = 'Вы отписались от заказов';
        $reply_markup = $this->replyKeyboardHide();
        $send_params = [
            'chat_id' => $params['chat_id'],
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $reply_markup,
            'is_group' => $params['is_group'],
        ];

        if (!empty($group)) {
            $send_params['chat_id'] = '-' . $group['group_id'];
            $send_params['is_group'] = 1;

            $subscription = [
                'group_id'  => $group['group_id'],
                'subscription'  => 'orders',
            ];

            $subscription_row = $this->telegram_group_subscriptions_model->getByField($subscription);
            if (!empty($subscription_row)) {
                $this->telegram_group_subscriptions_model->deleteById($subscription_row['id']);
            }
            else {
                $send_params['text'] = 'Вы не были подписаны на заказы';
            }
        }

        $this->botSendMessage($send_params);
    }


    /**
     * @param $params
     * @return array|null
     * @throws waException
     */
    public function checkUser($params)
    {
        $user = $this->telegram_user_model->getById($params['user_id']);

        if (empty($user) && !empty($params['user_id'])) {
            $user = array(
                'user_id'       => $params['user_id'],
                'chat_id'       => abs($params['chat_id']),
                'name'          => $params['name'],
                'in_group'      => false,
                'blocked'       => 0,
            );

            if (isset($params['is_bot'])) {
                $user['is_bot'] = $params['is_bot'];
            }

            try {
                $this->telegram_user_model->insert($user);
            } catch (Exception $e) {
                if (waSystemConfig::isDebug()) {
                    waLog::log("PARAMS:", 'telegram/steelrat-insert-errors.log');
                    waLog::log(print_r($params,true), 'telegram/steelrat-insert-errors.log');
                    waLog::log("USER:", 'telegram/steelrat-insert-errors.log');
                    waLog::log(print_r($user,true), 'telegram/steelrat-insert-errors.log');
                    waLog::log("================", 'telegram/steelrat-insert-errors.log');

                }
            }
        }
        return $user;
    }

    /**
     * @param $params
     * @return array|null
     * @throws waException
     */
    public function checkGroup($params)
    {

        if (empty(self::$settings['group_user_name'])) {
            $group = $this->telegram_group_model->getById(abs($params['chat_id']));
        }
        else {
            $group = $this->telegram_group_model->getByField([
                'group_id' => abs($params['chat_id']),
                'username' => self::$settings['group_user_name'],
            ]);
        }

        if (empty($group) && !empty($params['chat_id'])) {
            if ((empty(self::$settings['group_user_name']) || self::$settings['group_user_name'] == $params['chat_username'])) {
                $group = array(
                    'group_id'      => abs($params['chat_id']),
                    'title'         => $params['chat_title'],
                    'username'      => $params['chat_username'],
                );

                try {
                    $this->telegram_group_model->insert($group);
                } catch (Exception $e) {
                    if (waSystemConfig::isDebug()) {
                        waLog::log($params, 'telegram/steelrat-insert-errors.log');
                        waLog::log($group, 'telegram/steelrat-insert-errors.log');
                    }
                    return [];
                }
            }
            else {
                return [];
            }

         }
        else {
            if ($group['title'] != $params['chat_title']) {
                $group['title'] = $params['chat_title'];
                $this->telegram_group_model->updateById($group['group_id'], $group);
            }
        }

        return $group;
    }



    /**
     * @param $params
     * @return string
     * @throws waException
     */
    public function getReplyMarkup($params)
    {
        $user = $this->checkUser($params);

        $keyboard = self::getButtonsArray();

        $reply_markup = $this->replyKeyboardMarkup(
            [
                'inline_keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => false,
            ]
        );

        return $reply_markup;
    }

    /**
     * @param $params
     * @throws waException
     */
    public function botAction($params)
    {
        $reply_markup = $this->getReplyMarkup($params);

        $send_params = [
            'chat_id' => $params['chat_id'],
            'text' => $this->view->fetch('string:' . $params['action']['content']),
            'parse_mode' => 'HTML',
            'reply_markup' => $reply_markup,
            'is_group' => $params['is_group'],
        ];

        $this->botSendMessage($send_params);
    }

}