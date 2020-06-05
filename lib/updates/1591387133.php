<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 05.06.2020
 * Time: 22:59
 */

try {
    $path = wa()->getAppPath(null, 'telegram') . '/vendors/telegram-bot-sdk/';
    waFiles::delete($path, true);
}
catch (waException $e) {
    waLog::dump($e, 'telegram-update-error.log');
}