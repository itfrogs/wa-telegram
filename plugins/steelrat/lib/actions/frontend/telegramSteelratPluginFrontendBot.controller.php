<?php

use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\TelegramClient;
use Telegram\Bot\FileUpload\InputFile;

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
        $this->telegram = new telegramSteelratPluginApi();
    }

    /**
     * @throws waException
     */
    public function execute()
    {
        $plugin = self::getPlugin();
        $view = self::getView();

        //Передаем в переменную $result полную информацию о сообщении пользователя
        $result = $this->telegram->getWebhookUpdate();
        $result_array = $result->toArray();

        //waLog::dump($result, 'telegram/steelrat-get-webhook-update.log');

        $user_model = new telegramSteelratPluginUserModel();
        $book_model = new telegramSteelratPluginBookModel();

        $error = false;
        $sended = false;

        //Проверяем точно ли к нам стучится телеграм. Если в запросе есть необходимые данные, то выполняем действия.

        //if(isset($result_array['callback_query']) && isset($result_array['callback_query']['data']) && !empty($result_array['callback_query']['data'])){
        if(isset($result["message"]) && isset($result["message"]["text"]) && !empty($result["message"]["text"])){
            $this->params = array(
                //'text'          => isset($result_array['callback_query']['data']) ? $result["message"]["text"] : null, //Текст сообщения
                'text'          => isset($result["message"]["text"]) ? $result["message"]["text"] : null, //Текст сообщения
                'chat_id'       => $result_array["message"]["chat"]["id"], //Уникальный идентификатор пользователя
                'name'          => $result_array["message"]["from"]["username"], //Юзернейм пользователя
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
                            waLog::log('Пользователь ' . !empty($user['name']) ? $user['name'] : $user['chat_id'] . ' забанил бота и был отписан.', 'telegram/steelrat-ban.log');
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