<?php
return array(
    'telegram_steelrat_book' => array(
        'id' => array('int', 11, 'null' => 0),
        'text' => array('text', 'null' => 0),
        'links' => array('varchar', 100, 'null' => 0),
        ':keys' => array(
            'PRIMARY' => 'id',
        ),
    ),
    'telegram_steelrat_user' => array(
        'chat_id' => array('int', 11, 'null' => 0),
        'name' => array('varchar', 50, 'null' => 0),
        'book_id' => array('int', 11),
        ':keys' => array(
            'PRIMARY' => 'chat_id',
        ),
    ),
);
