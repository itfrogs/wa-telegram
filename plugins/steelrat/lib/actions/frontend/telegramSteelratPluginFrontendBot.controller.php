<?php

use GuzzleHttp\Client;
use Telegram\Bot\HttpClients\GuzzleHttpClient;
use Telegram\Bot\TelegramClient;

/**
 * Class telegramSteelratPluginFrontendBotController
 */

class telegramSteelratPluginFrontendBotController extends waController
{
    /**
     * @var
     */
    private $settings;

    /**
     * @var
     */
    private $params;

    /**
     * @var telegramApi
     */
    private $telegram;

    /**
     * @var telegramSteelratPlugin $plugin
     */
    private static $plugin;

    /**
     * @return telegramSteelratPlugin|waPlugin
     * @throws waException
     */
    private static function getPlugin()
    {
        if (!isset(self::$plugin)) {
            self::$plugin = wa('telegram')->getPlugin('steelrat');
        }
        return self::$plugin;
    }

    /**
     * @var waView $view
     */
    private static $view;

    /**
     * @return waSmarty3View|waView
     * @throws waException
     */
    private static function getView()
    {
        if (!isset(self::$view)) {
            self::$view = waSystem::getInstance()->getView();
        }
        return self::$view;
    }


    /**
     * telegramSteelratPluginFrontendBotController constructor.
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     * @throws waException
     */
    public function __construct()
    {
        $plugin = self::getPlugin();
        $this->settings = $plugin->getSettings();
        $this->telegram = new telegramApi($this->settings['key'], false, 'guzzle');

        $options = array(
            'headers' => [
                'User-Agent' => 'Telegram Bot PHP SDK v'.telegramApi::VERSION.' - (https://github.com/irazasyed/telegram-bot-sdk)',
            ],
        );

        if ($this->settings['use_socks5']) {
            $proxy = 'socks5://';
            if (!empty($this->settings['socks5_user']) && !empty($this->settings['socks5_password'])) {
                $proxy .= $this->settings['socks5_user'] . ':' . $this->settings['socks5_password'] . '@';
            }
            elseif (!empty($this->settings['socks5_user']) && empty($this->settings['socks5_password'])) {
                $proxy .= $this->settings['socks5_user'] . '@';
            }

            if (!empty($this->settings['socks5_address']) && !empty($this->settings['socks5_port'])) {
                $proxy .= $this->settings['socks5_address'] . ':' . $this->settings['socks5_port'];
            }
            else {
                unset($proxy);
            }

            if (isset($proxy)) {
                $options['curl'] =  array(
                    CURLOPT_PROXY => $proxy,
                    CURLOPT_HTTPPROXYTUNNEL => 1,
                    CURLOPT_HEADER => false,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_POST => 1,
                    CURLOPT_SSL_VERIFYPEER => false,

                );
            }
        }
        $client = new Client($options);
        $httpClientHandler = new GuzzleHttpClient($client);
        $this->telegram->setClient(new TelegramClient($httpClientHandler));

        //waRequest::isHttps()
    }

    /**
     * @throws waException
     */
    public function execute()
    {
        $plugin = self::getPlugin();
        $view = self::getView();

        //Передаем в переменную $result полную информацию о сообщении пользователя
        $result = $this->telegram->getWebhookUpdates();

        $user_model = new telegramSteelratPluginUserModel();
        $book_model = new telegramSteelratPluginBookModel();

        $error = false;
        $sended = false;

        //Проверяем точно ли к нам стучится телеграм. Если в запросе есть необходимые данные, то выполняем действия.
        if(isset($result["message"]) && isset($result["message"]["text"]) && !empty($result["message"]["text"])){
            $this->params = array(
                'text'          => isset($result["message"]["text"]) ? $result["message"]["text"] : null, //Текст сообщения
                'chat_id'       => $result["message"]["chat"]["id"], //Уникальный идентификатор пользователя
                'name'          => $result["message"]["from"]["username"], //Юзернейм пользователя
            );

            if ($this->params['text'] == "/start" || $this->params['text'] == "start") {
                $this->start($this->params);
            }elseif ($this->params['text'] == "/help" || $this->params['text'] == "help") {
                $this->help();
                $sended = true;
            }elseif ($this->params['text'] == "/map" || $this->params['text'] == "map") {
                $this->map();
                $sended = true;
            }elseif ($this->params['text'] == "/stop" || $this->params['text'] == "stop") {
                $this->stop($this->params);
                $sended = true;
            }elseif (is_numeric($this->params['text'])) {
                $this->params['book_id'] = intval($this->params['text']);
                if ($this->params['book_id'] > 0 && $this->params['book_id'] <351) {
                    $user = $user_model->getById($this->params['chat_id']);
                    if (empty($user)) {
                        $user = array(
                            'chat_id'   => $this->params['chat_id'],
                            'name'      => $this->params['name'],
                            'book_id'   => $this->params['book_id'],
                        );
                        $user_model->insert($user);
                    }
                    else {
                        $user['book_id'] = $this->params['book_id'];
                        $user_model->updateByField('chat_id', $this->params['chat_id'], $user);
                    }
                }
                else {
                    $error = 'Номер страницы за пределами книги';
                }
            }else{
                $reply = "По запросу \"<b>".$this->params['text']."</b>\" ничего не найдено.";
                $this->telegram->sendMessage([ 'chat_id' => $this->params['chat_id'], 'parse_mode'=> 'HTML', 'text' => $reply ]);
                $sended = true;
            }

            if (!$error && !$sended) {
                $user = $user_model->getById($this->params['chat_id']);
                if (empty($user)) {
                    $this->start($this->params);
                    $user = $user_model->getById($this->params['chat_id']);
                }

                if (!$user['book_id']) {
                    $text = $view->fetch($plugin->getPluginPath() . '/templates/start.html');
                    $keyboard = [
                        ['30']
                    ];
                }
                else {
                    $book = $book_model->getById($this->params['book_id']);
                    $text = html_entity_decode($book['text']);
                    $keyboard = [
                        json_decode($book['links']),
                    ];
                }

                $reply_markup = $this->telegram->replyKeyboardMarkup(
                    [
                        'keyboard' => $keyboard,
                        'resize_keyboard' => true,
                        'one_time_keyboard' => true,
                    ]
                );

                try {
                    $this->telegram->sendMessage([
                        'chat_id' => $this->params['chat_id'],
                        'text' => $text,
                        'reply_markup' => $reply_markup
                    ]);
                } catch (Exception $e) {
                    if ($e->getMessage() && $e->getMessage() == 'Forbidden: bot was blocked by the user') {
                        $this->stop($this->params);
                        if (waSystemConfig::isDebug()) {
                            waLog::log('Пользователь ' . !empty($user['name']) ? $user['name'] : $user['chat_id'] . ' забанил бота и был отписан.', 'telegram-steelrat-ban.log');
                        }
                    }
                }

            }
            elseif (!$sended && $error) {
                $this->telegram->sendMessage([ 'chat_id' => $this->params['chat_id'], 'text' => $error]);
            }
        }
        else{
            $this->telegram->sendMessage([ 'chat_id' => $this->params['chat_id'], 'text' => "Отправьте текстовое сообщение." ]);
        }

        return false;
    }

    /**
     *
     */
    private function start($params) {
        $user_model = new telegramSteelratPluginUserModel();

        $user = $user_model->getById($params['chat_id']);

        if (empty($user)) {
            $user = array(
                'chat_id'   => $params['chat_id'],
                'name'      => $params['name'],
                'book_id'   => null,
            );
            $user_model->insert($user);
        }
        else {
            $user['book_id'] = null;
            $user_model->updateByField('chat_id', $params['chat_id'], $user);
        }
    }

    /**
     * @param $params
     */
    private function stop($params) {
        $user_model = new telegramSteelratPluginUserModel();

        $user = $user_model->getById($params['chat_id']);

        $reply_markup = $this->telegram->replyKeyboardHide();

        if (!empty($user)) {
            $user_model->deleteById($params['chat_id']);
            $text = "Вы удалены из базы.";
        }
        else {
            $text = "Вас и не было в базе. Не дурите бота, он обидчивый.";
        }
        $this->telegram->sendMessage(
            [
                'chat_id' => $this->params['chat_id'],
                'text' => $text,
                'reply_markup' => $reply_markup,
            ]
        );
    }

    /**
     * @throws waException
     */
    private function help() {
        $plugin = self::getPlugin();
        $view = self::getView();
        $reply = $view->fetch($plugin->getPluginPath() . '/templates/help.html');
        $this->telegram->sendMessage([ 'chat_id' => $this->params['chat_id'], 'parse_mode'=> 'HTML', 'text' => $reply]);
    }

    /**
     * @throws waException
     */
    private function map() {
        $plugin = self::getPlugin();
        $image_path = $plugin::getPluginPath() . '/img/map.png';
        $this->telegram->sendPhoto([ 'chat_id' => $this->params['chat_id'], 'photo'=> $image_path, 'caption' => 'Карта Скралдеспенда' ]);
    }
}