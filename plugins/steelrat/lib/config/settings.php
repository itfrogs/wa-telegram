<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 4/3/18
 * Time: 12:06 PM
 */

return array(
    'https' => array(
        'title' => _wp('HTTPS check'),
        'description' => _wp('If not https, the bot will not work.'),
        'control_type' => waHtmlControl::CUSTOM . ' ' . 'telegramSteelratPlugin::getHttpsCheckControl',
        'subject' => 'basic_settings',
    ),
    'key' => array(
        'value' => '',
        'title' => _wp('Bot token'),
        'description' => _wp('Set the API key.'),
        'control_type' => 'text',
        'subject' => 'basic_settings',
    ),
    'use_socks5' => array(
        'value' => 0,
        'title' => _wp('Use socks5 proxy'),
        'description' => _wp('If enabled, the socks5 proxy will be used.'),
        'control_type' => waHtmlControl::CHECKBOX,
        'subject' => 'basic_settings',
    ),
    'socks5_address' => array(
        'value' => '127.0.0.1',
        'title' => _wp('Socks5 proxy address'),
        'description' => _wp('If empty, the proxy will be disabled.'),
        'control_type' => waHtmlControl::INPUT,
        'subject' => 'basic_settings',
    ),
    'socks5_port' => array(
        'value' => 443,
        'title' => _wp('Socks5 proxy port'),
        'description' => _wp('If empty, the proxy will be disabled.'),
        'control_type' => waHtmlControl::INPUT,
        'subject' => 'basic_settings',
    ),
    'socks5_user' => array(
        'value' => 'anonimous',
        'title' => _wp('Socks5 proxy user'),
        'description' => _wp('If empty, the login and password will not be used.'),
        'control_type' => waHtmlControl::INPUT,
        'subject' => 'basic_settings',
    ),
    'socks5_password' => array(
        'value' => '',
        'title' => _wp('Socks5 proxy password'),
        'description' => _wp('If empty, the password will not be used.'),
        'control_type' => waHtmlControl::INPUT,
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
        'description' => _wp('Internet address for registering a bot link. If api.telegram.org is blocked for you, use the Tor Browser or other method of bypass blocking to activate the bot.'),
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