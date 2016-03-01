<?php

namespace OLOG\CRUD;

class CRUDConfigReader
{
    const CONFIG_ROOT = 'PHPCRUD';
    const CONFIG_KEY_MODEL_CLASS_NAME = 'MODEL_CLASS_NAME';
    const CONFIG_KEY_LIST_CONFIG = 'LIST_CONFIG';
    const CONFIG_KEY_EDITOR_CONFIG = 'EDITOR_CONFIG';
    const CONFIG_KEY_AUTH_PROVIDER_CLASS_NAME = 'AUTH_PROVIDER_CLASS_NAME';
    const CONFIG_KEY_PERMISSIONS_ARR = 'PERMISSIONS_ARR';

    static public function getAuthProviderClassName(){
        $auth_provider_class_name = self::getConfigForKey(self::CONFIG_KEY_AUTH_PROVIDER_CLASS_NAME);

        \OLOG\Helpers::assert($auth_provider_class_name, 'auth provider class name missing');
        return $auth_provider_class_name;
    }

    /**
     * Возвращает массив кодов пермишенов пользователя, нужных для редактирования модели.
     * Если такой настройки в пузыре нет - выбрасывает исключение. Работать с крудом можно только с правами.
     * @param $bubble_key
     * @return mixed
     * @throws \Exception
     */
    static public function getPermissionsArrForBubbleKey($bubble_key){
        $config_arr = self::getConfigForKey($bubble_key);

        \OLOG\Helpers::assert(array_key_exists(self::CONFIG_KEY_PERMISSIONS_ARR, $config_arr));
        return $config_arr[self::CONFIG_KEY_PERMISSIONS_ARR];
    }

    /**
     * Выбрасывает исключение если запрошенного ключа в конфиге нет
     * @param $bubble_key
     * @return mixed
     * @throws \Exception
     */
    public static function getConfigForKey($bubble_key)
    {
        $config_arr = \OLOG\ConfWrapper::value(self::CONFIG_ROOT . '.' . $bubble_key);
        \OLOG\Helpers::assert($config_arr);

        return $config_arr;
    }

    public static function getListConfigForKey($config_key){
        $config_arr = self::getConfigForKey($config_key);

        \OLOG\Helpers::assert(array_key_exists(self::CONFIG_KEY_LIST_CONFIG, $config_arr));
        return $config_arr[self::CONFIG_KEY_LIST_CONFIG];
    }

    public static function getEditorConfigForKey($config_key){
        $config_arr = self::getConfigForKey($config_key);

        \OLOG\Helpers::assert(array_key_exists(self::CONFIG_KEY_EDITOR_CONFIG, $config_arr));
        return $config_arr[self::CONFIG_KEY_EDITOR_CONFIG];
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