<?php

namespace OLOG\CRUD;

class CRUDConfigReader
{
    const CONFIG_ROOT = 'PHPCRUD';
    const CONFIG_KEY_MODEL_CLASS_NAME = 'MODEL_CLASS_NAME';
    const CONFIG_KEY_LIST = 'LIST';
    const CONFIG_KEY_EDITOR = 'EDITOR';

    /**
     * Выбрасывает исключение если запрошенного ключа в конфиге нет
     * @param $config_key
     * @return mixed
     * @throws \Exception
     */
    public static function getConfigForKey($config_key)
    {
        $config_arr = \OLOG\ConfWrapper::value(self::CONFIG_ROOT . '.' . $config_key);
        \OLOG\Helpers::assert($config_arr);

        return $config_arr;
    }

    public static function getListConfigForKey($config_key){
        $config_arr = self::getConfigForKey($config_key);

        \OLOG\Helpers::assert(array_key_exists(self::CONFIG_KEY_LIST, $config_arr));
        return $config_arr[self::CONFIG_KEY_LIST];
    }

    public static function getEditorConfigForKey($config_key){
        $config_arr = self::getConfigForKey($config_key);

        \OLOG\Helpers::assert(array_key_exists(self::CONFIG_KEY_EDITOR, $config_arr));
        return $config_arr[self::CONFIG_KEY_EDITOR];
    }

    /**
     * Выбрасывает исключение если нет конфига для такого ключа или в конфиге не указано имя класса модели.
     * @param $config_key
     * @return mixed
     * @throws \Exception
     */
    public static function getModelClassNameForKey($config_key)
    {
        $config_arr = self::getConfigForKey($config_key);

        $model_class_name = $config_arr[CRUDConfigReader::CONFIG_KEY_MODEL_CLASS_NAME];
        \OLOG\Helpers::assert($model_class_name);

        return $model_class_name;
    }
}