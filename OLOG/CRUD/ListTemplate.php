<?php

namespace OLOG\CRUD;


class ListTemplate
{
    static public function render($bubble_key, $context_arr, $list_title = ''){
        //$config_arr = CRUDController::getConfigForKey($config_key);
        $model_class_name = CRUDConfigReader::getModelClassNameForKey($bubble_key);

        if (!$list_title){
            // TODO: get title from config
            $list_title = $model_class_name;
        }

        //
        // готовим список ID объектов для вывода
        //

        $filter = '';
        if (isset($_GET['filter'])){
            $filter = $_GET['filter'];
        }
        $objs_ids_arr = \OLOG\CRUD\Helpers::getObjIdsArrForClassName($model_class_name, $context_arr, $filter);

        //
        // готовим список полей, которые будем выводить в таблицу
        //

        $reflect = new \ReflectionClass($model_class_name);
        $props_arr = array();

        foreach ($reflect->getProperties() as $prop_obj) {
            if (!$prop_obj->isStatic()) { // игнорируем статические свойства класса - они относятся не к объекту, а только к классу (http://www.php.net/manual/en/language.oop5.static.php), и в них хранятся настройки ActiveRecord и CRUD
                $prop_obj->setAccessible(true);
                $props_arr[] = $prop_obj;
            }
        }

        // TODO
        /*
        $crud_table_fields_arr = array();

        if (property_exists($model_class_name, 'crud_table_fields_arr') && (count($model_class_name::$crud_table_fields_arr) > 0)) {
            foreach ($props_arr as $delta => $property_obj) {
                if (!in_array($property_obj->getName(), $model_class_name::$crud_table_fields_arr)) {
                    unset($props_arr[$delta]);
                }
            }
        }
        */

        /* TODO (remove??)
        $container_models_arr = array();
        if (property_exists($model_class_name, 'crud_container_model')) {
            $container_models_arr = $model_class_name::$crud_container_model;
        }
        */

        //
        // вывод таблицы
        //

        echo '<h2>' . $list_title . '</h2>';

        // LIST TOOLBAR
        echo '<div>';

        /* TODO restore check
        if (\OLOG\CRUD\Helpers::canDisplayCreateButton($model_class_name, $context_arr)) {
        */
            echo ' <a class="btn btn-default glyphicon glyphicon-plus" href="' . Sanitize::sanitizeUrl(CRUDController::addAction(\OLOG\Router::GET_URL, $bubble_key)) . '?' . http_build_query(array('context_arr' => $context_arr)) . '"></a>';
        /*
        }
        */
        echo '</div>';

        /* TODO
        if (isset($model_class_name::$crud_model_title_field)) {
            if (isset($model_class_name::$crud_allow_search)) {
                if ($model_class_name::$crud_allow_search == true) {
                    echo '<div class="pull-right" style="margin-top: 25px;"><form action="' . \Sportbox\Helpers::uri_no_getform() . '"><input name="filter" value="' . $filter . '"><input type="submit" value="искать"></form></div>';
                }
            }
        }
        */

        // TODO: not needed?
        //echo '<div class="clearfix"></div>';


        // create fast add block

        // чтобы создать форму быстрого добавления в классе должны быть следующие поля:
        // public static $crud_fast_create_field_name = 'answer_text', где answer_text - имя выводимого поля
        /* TODO
        if (property_exists($model_class_name, 'crud_fast_create_field_name')) {
            $fast_create_field_name = $model_class_name::$crud_fast_create_field_name;

            $label_field_name = \Sportbox\CRUD\Helpers::getTitleForField($model_class_name, $fast_create_field_name);
            $create_url = \Sportbox\CRUD\ControllerCRUD::getCreateUrl($model_class_name);

            echo '<form role="form" method="post" class="form-inline" action="' . $create_url . '">';
            echo '<div class="form-group">';
            echo '<input placeholder="' . $label_field_name . '" name="' . $fast_create_field_name . '" class="form-control"/>';
            echo '<button type="submit" class="btn btn-default">Добавить</button>';

            foreach ($context_arr as $context_arr_key => $context_arr_value) {
                echo '<input type="hidden" name="' . $context_arr_key . '" value="' . $context_arr_value . '">';
            }

            echo '<input type="hidden" name="destination" value="' . \Sportbox\Helpers::uri_no_getform() . '">';
            echo '</div>';
            echo '</form>';

        }
        */

        if (count($objs_ids_arr) > 0) {

            echo '<table class="table table-hover">';
            echo '<thead><tr>';

            foreach ($props_arr as $prop_obj) {
                // TODO
                //$table_title = \OLOG\CRUD\Helpers::getTitleForField($model_class_name, $prop_obj->getName());
                $table_title = $prop_obj->getName();

                echo '<th>' . $table_title . '</th>';
            }
            echo '<th></th></tr></thead>';
            echo '<tbody>';

            foreach ($objs_ids_arr as $obj_id) {
                $obj_obj = ObjectLoader::createAndLoadObject($model_class_name, $obj_id);

                echo '<tr>';
                foreach ($props_arr as $prop_obj) {
                    $title = $prop_obj->getValue($obj_obj);

                    /* TODO: completely rewrite
                    $link_field_key = array_search($prop_obj->getName(), array_values($container_models_arr));
                    $roles = \Sportbox\CRUD\Widgets::getFieldWidgetName($prop_obj->getName(), $obj_obj);

                    $title = "";

                    if ($link_field_key !== false) {
                        $container_array_keys = array_keys($container_models_arr);
                        $container_model = $container_array_keys[$link_field_key];

                        $container_obj = \Sportbox\CRUD\Helpers::createAndLoadObject($container_model, $prop_obj->getValue($obj_obj));

                        if (method_exists($container_obj, 'getTitle')) {
                            $title .= $container_obj->getTitle() . " ";
                        }

                        $title .= "(" . $container_obj->getId() . ")";
                    }
                    else if ($roles == "options") {
                        $role = \Sportbox\CRUD\Widgets::getFieldWidgetOptionsArr($prop_obj->getName(), $obj_obj);
                        if (array_key_exists($prop_obj->getValue($obj_obj), $role)) {
                            $title = $role[$prop_obj->getValue($obj_obj)];
                        }
                    }
                    else {
                        $title = \Sportbox\CRUD\Widgets::renderListFieldWithWidget($prop_obj->getName(), $obj_obj);

                        // если это поле с названием модели - делаем его значение ссылкой на редактирование
                        // если же значение не содержит видимымх символов - выводим кнопку редактирования (чтобы не остаться без ссылки)
                        if (property_exists($model_class_name, 'crud_model_title_field')) {
                            if ($prop_obj->getName() == $model_class_name::$crud_model_title_field){
                                if (\Sportbox\CRUD\Helpers::stringCanBeUsedAsLinkText($title)) {
                                    $edit_url = \Sportbox\CRUD\ControllerCRUD::getEditUrl($model_class_name, $obj_id);
                                    $title = '<a href="' . $edit_url . '">' . $title . '</a>';
                                    $show_edit_button = false;
                                }
                            }
                        }

                    }
                    */

                    echo '<td>' . Sanitize::sanitizeTagContent($title) . '</td>';
                }

                echo '<td style="text-align: right;">';

                // если есть конфиг редактора - выводим ссылку на первый таб из этого конфига
                $editor_config_arr = CRUDConfigReader::getEditorConfigForKey($bubble_key);
                if ($editor_config_arr) {
                    $editor_tabs_arr = array_keys($editor_config_arr);
                    if (count($editor_tabs_arr)) {
                        $tab_key = $editor_tabs_arr[0];
                        $edit_url = \OLOG\CRUD\CRUDController::editAction(\OLOG\Router::GET_URL, $bubble_key, $obj_id, $tab_key);
                        echo '<a class="glyphicon glyphicon-edit" href="' . $edit_url . '"></a> ';
                    }
                }

                if ($model_class_name instanceof \OLOG\Model\InterfaceDelete){
                    $delete_url = \OLOG\CRUD\CRUDController::getDeleteUrl($model_class_name, $obj_id);
                    echo '<a class="glyphicon glyphicon-remove" href="' . $delete_url . '?destination=' . urlencode($_SERVER['REQUEST_URI']) . '" onclick="return window.confirm(\'Уверены?\')"></a>';
                }

                echo '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';

            /* TODO
            echo \Sportbox\Pager::renderPager(count($objs_ids_arr));
            */
        }

    }
}