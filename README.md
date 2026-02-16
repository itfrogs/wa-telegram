# Боты Telegram для Webasyst

Платформа для создания Telegram-ботов внутри Webasyst. Само по себе приложение не имеет видимого интерфейса — функциональность добавляется через плагины, каждый из которых является отдельным ботом.

Приложение распространяется бесплатно: [github.com/itfrogs/wa-telegram](https://github.com/itfrogs/wa-telegram).
В репозитории есть бесплатный плагин **steelrat** — пример взаимодействия плагина с приложением.

## Требования

- PHP **7.4** и выше (рекомендуется **8.1+**)
- Сайт должен быть доступен по **HTTPS** из глобальной сети

## Установка

1. Зарегистрируйте бота в Telegram через [@BotFather](https://t.me/BotFather) (`/newbot`) и получите токен.
2. В разделе «Сайт» задайте скрытое поселение для приложения. Рекомендуем сложный URL, например `telegram4e0214025588cde184591b300784b9d4`.
3. Установите нужный плагин и укажите в нём токен бота.

## Использование в плагине

```php
$telegram = new telegramApi(BOT_TOKEN);
```

После этого доступны все методы SDK. Документация: [telegram-bot-sdk.com](https://telegram-bot-sdk.com/).

## Готовые плагины

- **Уведомления и чат** — уведомления о заказах, чат с клиентами
- **Авторизация** — вход на сайт через Telegram
- **Мониторинг заказов** — отслеживание статусов заказов

## Справочник методов telegramApi

Класс `telegramApi` наследует `Telegram\Bot\Api` из SDK 3.9 (PHP 7.4) и 3.15 (PHP 8.1+).

### Отправка сообщений

| Метод | Описание | Обязательные параметры |
|-------|----------|------------------------|
| `sendMessage(array $params)` | Отправить текстовое сообщение | chat_id, text |
| `sendPhoto(array $params)` | Отправить фото | chat_id, photo |
| `sendDocument(array $params)` | Отправить файл/документ | chat_id, document |
| `sendAudio(array $params)` | Отправить аудио | chat_id, audio |
| `sendVideo(array $params)` | Отправить видео (mp4) | chat_id, video |
| `sendVoice(array $params)` | Отправить голосовое сообщение | chat_id, voice |
| `sendAnimation(array $params)` | Отправить GIF или видео без звука | chat_id, animation |
| `sendMediaGroup(array $params)` | Отправить группу медиафайлов альбомом | chat_id, media |
| `sendContact(array $params)` | Отправить контакт с телефоном | chat_id, phone_number, first_name |
| `sendPoll(array $params)` | Отправить опрос | chat_id, question, options |
| `sendDice(array $params)` | Отправить кубик (случайное значение 1–6) | chat_id |
| `sendChatAction(array $params)` | Показать действие в чате (набор текста, загрузка…) | chat_id, action |
| `forwardMessage(array $params)` | Переслать сообщение | chat_id, from_chat_id, message_id |
| `copyMessage(array $params)` | Скопировать сообщение без ссылки на оригинал | chat_id, from_chat_id, message_id |

### Вебхук

| Метод | Описание |
|-------|----------|
| `setWebhook(array $params)` | Установить вебхук (url — обязательно, HTTPS) |
| `deleteWebhook()` | Удалить вебхук, переключиться на getUpdates |
| `getWebhookInfo()` | Получить статус текущего вебхука |
| `getWebhookUpdate()` | Получить входящее обновление от Telegram |

### Получение обновлений

| Метод | Описание |
|-------|----------|
| `getUpdates(array $params)` | Получить обновления через long polling (offset, limit, timeout) |
| `getMe()` | Проверить токен, получить информацию о боте |

### Редактирование и удаление сообщений

| Метод | Описание |
|-------|----------|
| `editMessageText(array $params)` | Изменить текст отправленного сообщения |
| `editMessageCaption(array $params)` | Изменить подпись к медиасообщению |
| `editMessageMedia(array $params)` | Заменить медиафайл в сообщении |
| `editMessageReplyMarkup(array $params)` | Изменить inline-клавиатуру сообщения |
| `deleteMessage(array $params)` | Удалить сообщение (chat_id, message_id) |
| `deleteMessages(array $params)` | Удалить несколько сообщений за раз (до 100) |

### Клавиатуры и callback

| Метод | Описание |
|-------|----------|
| `replyKeyboardMarkup(array $params)` | Создать reply-клавиатуру (keyboard, resize_keyboard, one_time_keyboard) |
| `replyKeyboardHide(array $params)` | Скрыть reply-клавиатуру |
| `forceReply(array $params)` | Принудительный запрос ответа от пользователя |
| `answerCallbackQuery(array $params)` | Ответить на нажатие кнопки inline-клавиатуры |
| `answerInlineQuery(array $params)` | Ответить на inline-запрос (до 50 результатов) |

### Файлы

| Метод | Описание |
|-------|----------|
| `getFile(array $params)` | Получить информацию о файле по file_id для скачивания |
| `getUserProfilePhotos(array $params)` | Получить список фотографий профиля пользователя |

### Команды бота

| Метод | Описание |
|-------|----------|
| `setMyCommands(array $params)` | Установить список команд бота |
| `getMyCommands(array $params)` | Получить текущий список команд |
| `deleteMyCommands(array $params)` | Удалить список команд |

### Низкоуровневые методы

| Метод | Описание |
|-------|----------|
| `telegramPost($endpoint, $params, $fileUpload)` | Прямой POST-запрос к Telegram API |
| `telegramGet($endpoint, $params)` | Прямой GET-запрос к Telegram API |
| `getBot($config)` | Получить BotsManager для управления несколькими ботами |
| `getGuzzleClientHandler($options)` | Получить GuzzleHttpClient с кастомными настройками |

## Ссылки

- [telegram-bot-sdk.com](https://telegram-bot-sdk.com/docs/getting-started/introduction) — документация SDK
- [core.telegram.org/bots/api](https://core.telegram.org/bots/api) — Telegram Bot API
