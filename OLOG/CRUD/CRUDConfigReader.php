<?php

namespace OLOG\CRUD;

class CRUDConfigReader
{
    //const KEY_ROOT = 'PHPCRUD';
    //const KEY_ELEMENTS = 'ELEMENTS';
    //const KEY_MODEL_CLASS_NAME = 'MODEL_CLASS_NAME';
    //const KEY_LIST_CONFIG = 'LIST_CONFIG';
    //const KEY_EDITOR_CONFIG = 'EDITOR_CONFIG';
    //const KEY_AUTH_PROVIDER_CLASS_NAME = 'AUTH_PROVIDER_CLASS_NAME';
    //const KEY_PERMISSIONS_ARR = 'PERMISSIONS_ARR';

    /*
    static public function getElements($config_arr){
        \OLOG\Assert::assert($config_arr[CRUDConfigReader::KEY_ELEMENTS]);
        return $config_arr[CRUDConfigReader::KEY_ELEMENTS];
    }
    */

    /*
    static public function getAuthProviderClassName(){
        $auth_provider_class_name = self::getBubbleConfig(self::KEY_AUTH_PROVIDER_CLASS_NAME);

        \OLOG\Assert::assert($auth_provider_class_name, 'auth provider class name missing');
        return $auth_provider_class_name;
    }
    */

    /**
     * Возвращает массив кодов пермишенов пользователя, нужных для редактирования модели.
     * Если такой настройки в пузыре нет - выбрасывает исключение. Работать с крудом можно только с правами.
     * @param $bubble_key
     * @return mixed
     * @throws \Exception
     */
    /*
    static public function getPermissionsArrForBubbleKey($bubble_key){
        $config_arr = self::getBubbleConfig($bubble_key);

        \OLOG\Assert::assert(array_key_exists(self::KEY_PERMISSIONS_ARR, $config_arr));
        return $config_arr[self::KEY_PERMISSIONS_ARR];
    }
    */

    static public function getRequiredSubkey(array $config_arr, $key){
        \OLOG\Assert::assert(array_key_exists($key, $config_arr), 'Missing required subkey ' . $key);
        return $config_arr[$key];
    }

    static public function getOptionalSubkey(array $config_arr, $key, $default){
        if (!array_key_exists($key, $config_arr)){
            return $default;
        }

        return $config_arr[$key];
    }

    /**
     * Выбрасывает исключение если запрошенного ключа в конфиге нет
     * @param $bubble_key
     * @return mixed
     * @throws \Exception
     */
    /*
    public static function getBubbleConfig($bubble_key)
    {
        $config_arr = \OLOG\ConfWrapper::value(self::KEY_ROOT . '.' . $bubble_key);
        \OLOG\Assert::assert($config_arr);

        return $config_arr;
    }
    */

    /*
    public static function getListConfigForKey($config_key){
        $config_arr = self::getBubbleConfig($config_key);

        \OLOG\Assert::assert(array_key_exists(self::KEY_LIST_CONFIG, $config_arr));
        return $config_arr[self::KEY_LIST_CONFIG];
    }

    public static function getEditorConfigForKey($config_key){
        $config_arr = self::getBubbleConfig($config_key);

        \OLOG\Assert::assert(array_key_exists(self::KEY_EDITOR_CONFIG, $config_arr));
        return $config_arr[self::KEY_EDITOR_CONFIG];
    }
    */

    /**
     * Выбрасывает исключение если нет конфига для такого ключа или в конфиге не указано имя класса модели.
     * @param $bubble_key
     * @return mixed
     * @throws \Exception
     */
    /*
    public static function getModelClassNameForBubble($bubble_key)
    {
        $config_arr = self::getBubbleConfig($bubble_key);

        $model_class_name = $config_arr[CRUDConfigReader::KEY_MODEL_CLASS_NAME];
        \OLOG\Assert::assert($model_class_name);

        return $model_class_name;
    }
    */
}