<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 21.12.2025
 * Time: 20:16
 */

try {
    $path = wa()->getAppPath(null, 'telegram') . '/lib/vendors/telegram-bot-sdk-3.14';
    waFiles::delete($path, true);
}
catch (waException $e) {
    waLog::dump($e, 'telegram/telegram-update-error.log');
}