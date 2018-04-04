<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 4/3/18
 * Time: 12:06 PM
 */

return array(
    'key' => array(
        'title' => _wp('Bot token'),
        'description' => _wp('Set the API key.'),
        'control_type' => 'text',
        'subject' => 'basic_settings',
    ),
    'feedback' => array(
        'title' => _wp('Ask for technical support'),
        'description' => _wp('Click on the link to contact the developer.'),
        'control_type' => waHtmlControl::CUSTOM . ' ' . 'telegramSteelratPlugin::getFeedbackControl',
        'subject' => 'info_settings',
    ),


);