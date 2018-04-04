<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 4/3/18
 * Time: 12:06 PM
 */

return array(
    'key' => array(
        'value' => '',
        'title' => _wp('Bot token'),
        'description' => _wp('Set the API key.'),
        'control_type' => 'text',
        'subject' => 'basic_settings',
    ),
    'url' => array(
        'title' => _wp('Bot URL'),
        'description' => _wp('The Internet address on which the bot works.'),
        'control_type' => waHtmlControl::CUSTOM . ' ' . 'telegramSteelratPlugin::getBotUrlControl',
        'subject' => 'info_settings',
    ),
    'webhook' => array(
        'title' => _wp('setWebhook URL'),
        'description' => _wp('Internet address for registering a bot link.'),
        'control_type' => waHtmlControl::CUSTOM . ' ' . 'telegramSteelratPlugin::getWebhookUrlControl',
        'subject' => 'info_settings',
    ),
    'feedback' => array(
        'title' => _wp('Ask for technical support'),
        'description' => _wp('Click on the link to contact the developer.'),
        'control_type' => waHtmlControl::CUSTOM . ' ' . 'telegramSteelratPlugin::getFeedbackControl',
        'subject' => 'info_settings',
    ),


);