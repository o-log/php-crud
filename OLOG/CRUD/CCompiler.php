<?php

namespace OLOG\CRUD;



class CCompiler {

    const NULL_STRING = 'NULLSTRING';

    /**
     * компиляция строки: разворачивание обращений к полям объектов
     * @param $str
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public static function compile($str, array $data)
    {
        if (is_callable($str)){
            return $str($data['this']);
        }

        // TODO: clean and finish

        $matches = [];

        if (array_key_exists('this', $data)) {
            $_this = $data['this'];
            if (CInternalFieldsAccess::objectHasProperty($_this, $str)){
                return CInternalFieldsAccess::getObjectFieldValue($_this, $str);
            }
        }

        // сначала подставляем значения в самых внутренних фигурных скобках, потом которые снаружи, и так пока все скобки не будут заменены
        // поддерживается два вида выражений:
        // - {obj->field} заменяется на значение поля field объекта obj. obj - это ключ массива data, т.е. здесь можно использовать такие строки, которые передаются сюда вызывающими функциями
        // -- обычно виджеты передают объект, который показывается в виджете, с именем this
        // - {class_name.id->field} заменяется на значение поля field объекта класса class_name с идентификатором id
        while (preg_match('@{([^}{]+)}@', $str, $matches)){
            $expression = $matches[1];
            $replacement = 'UNKNOWN_EXPRESSION';

            $magic_matches = [];
            if (preg_match('@^(\w+)\->([\w()]+)$@', $expression, $magic_matches)){
                $obj_key_in_data = $magic_matches[1];
                $obj_field_name = $magic_matches[2];

                $obj = $data[$obj_key_in_data];

                $replacement = self::getReplacement($obj, $obj_field_name);
            }

            if (preg_match('@^([\w\\\\]+)\.(\w+)->([\w()]+)$@', $expression, $magic_matches)){
                $class_name = $magic_matches[1];
                $obj_id = $magic_matches[2];
                $obj_field_name = $magic_matches[3];

                if ($obj_id != self::NULL_STRING) { // TODO: review?
                    $obj = CInternalObjectLoader::createAndLoadObject($class_name, $obj_id);
                    $replacement = self::getReplacement($obj, $obj_field_name);
                } else {
                    $replacement = ''; // пустая строка для случаев типа '{' . Sport::class . '.{this->sport_id}->title}'  и this->sport_id не установленно
                }
            }

            // здесь заменяем только первое вхождение, потому что выше мы обрабатывали только первое вхождение
            // если не сделать это ограничение - вот такое выражение
            // new \OLOG\CRUD\CRUDTableWidgetText('{this->video_width}x{this->video_height}'))
            // выдаст "video_width Х video_width"
            // т.е. для прочитает первые скобки, а потом два заменит на результат и первые, и вторые
            $str = preg_replace('@{([^}{]+)}@', $replacement, $str, 1);
        }
        if (self::NULL_STRING == $str) {
            return null;
        }
        return $str;
    }

    public static function getReplacement($obj, $obj_field_name)
    {
        assert($obj);

        $matches = [];
        if (preg_match('@^(\w+)\(\)$@', $obj_field_name, $matches)) { // имя поля заканчивается скобками - значит это имя метода
            $method_name = $matches[1];
            assert(method_exists($obj, $method_name));
            $replacement = call_user_func([$obj, $method_name]);
        } else {
            $replacement = CInternalFieldsAccess::getObjectFieldValue($obj, $obj_field_name);
        }
        if (is_null($replacement)) {
            $replacement = self::NULL_STRING;
        }

        return $replacement;
    }
}
