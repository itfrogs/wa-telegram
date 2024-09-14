<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 15.09.2024
 * Time: 01:34
 */

try {
    $path = wa()->getAppPath(null, 'telegram') . '/js/telegram.min.js';
    waFiles::delete($path, true);
}
catch (waException $e) {
    waLog::dump($e, 'telegram/telegram-update-error.log');
}