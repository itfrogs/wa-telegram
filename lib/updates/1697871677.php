<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 21.10.2023
 * Time: 10:01
 */

try {
    $path = wa()->getAppPath(null, 'telegram') . '/lib/vendors/telegram-bot-sdk-2.0/';
    waFiles::delete($path, true);
}
catch (waException $e) {
    waLog::dump($e, 'telegram/telegram-update-error.log');
}