<?php

namespace OLOG\CRUD;

class ConfigReader
{
    const CONFIG_ROOT = 'phpcrud';
    const CONFIG_KEY_MODEL_CLASS_NAME = 'model_class_name';

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

    /**
     * Выбрасывает исключение если нет конфига для такого ключа или в конфиге не указано имя класса модели.
     * @param $config_key
     * @return mixed
     * @throws \Exception
     */
    public static function getModelClassNameForConfigKey($config_key)
    {
        $config_arr = self::getConfigForKey($config_key);

        $model_class_name = $config_arr[ConfigReader::CONFIG_KEY_MODEL_CLASS_NAME];
        \OLOG\Helpers::assert($model_class_name);

        return $model_class_name;
    }
}