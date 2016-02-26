<?php

namespace CRUDDemo;

use OLOG\CRUD\ConfigReader;

class Config
{
    const DB_NAME_PHPCRUDDEMO = 'phpcrud';

    public static function get()
    {
        $conf[ConfigReader::CONFIG_ROOT] = [
            'node' => [
                ConfigReader::CONFIG_KEY_MODEL_CLASS_NAME => \CRUDDemo\Node::class,
                'editor' => [
                    'tab_1' => [
                        'fields' => [
                            'title' => [

                            ]
                        ]
                    ]
                ]

            ]
        ];

        $conf['return_false_if_no_route'] = true; // for local php server

        $conf['db'] = [
            self::DB_NAME_PHPCRUDDEMO => [
                'host' => 'localhost',
                'db_name' => 'phpcrud',
                'user' => 'root',
                'pass' => '1'
            ]
        ];

        return $conf;
    }
}