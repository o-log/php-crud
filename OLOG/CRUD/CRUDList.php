<?php

namespace OLOG\CRUD;

class CRUDList
{
    const KEY_LIST_COLUMNS = 'LIST_COLUMNS';
    const OPERATION_ADD_MODEL = 'OPERATION_ADD_MODEL';
    const OPERATION_DELETE_MODEL = 'OPERATION_DELETE_MODEL';

    /*
    static protected function addModelOperation($model_class_name){
        \OLOG\Model\Helper::exceptionIfClassNotImplementsInterface($model_class_name, \OLOG\Model\InterfaceSave::class);

        // TODO: must read class_name from form!!! form may be placed on the other object page or any page

        $new_prop_values_arr = array();
        $reflect = new \ReflectionClass($model_class_name);

        foreach ($reflect->getProperties() as $prop_obj) {
            if (!$prop_obj->isStatic()) {
                $prop_name = $prop_obj->getName();
                if (array_key_exists($prop_name, $_POST)) {
                    // Проверка на заполнение обязательных полей делается на уровне СУБД, через нот нулл в таблице
                    $new_prop_values_arr[$prop_name] = $_POST[$prop_name];
                }
            }
        }

        //
        // сохранение
        //

        $obj = new $model_class_name;

        $obj = FieldsAccess::setObjectFieldsFromArray($obj, $new_prop_values_arr);
        $obj->save();

        \OLOG\Redirects::redirectToSelf();
    }
    */

    // TODO: move to library
    static public function getRequiredPostValue($key){
        $value = '';

        if (array_key_exists($key, $_POST)){
            $value = $_POST[$key];
        }

        \OLOG\Assert::assert($value != '', 'Missing required POST field ' . $key);

        return $value;
    }

    // TODO: move to library
    static public function getOptionalPostValue($key, $default = ''){
        $value = '';
        
        if (array_key_exists($key, $_POST)){
            $value = $_POST[$key];
        }
        
        if ($value == ''){
            $value = $default;
        }

        return $value;
    }

    static protected function deleteModelOperation($model_class_name){
        \OLOG\Model\Helper::exceptionIfClassNotImplementsInterface($model_class_name, \OLOG\Model\InterfaceDelete::class);

        $model_class_name = self::getRequiredPostValue('_class_name'); // TODO: constant for field name
        $model_id = self::getRequiredPostValue('_id'); // TODO: constant for field name

        $obj = ObjectLoader::createAndLoadObject($model_class_name, $model_id);
        $obj->delete();

        \OLOG\Redirects::redirectToSelf();
    }

    /**
     * @param $model_class_name
     * @param $creation_form_html
     * @param $columns_arr
     * @param $element_config_arr
     * @param array $context_arr
     * @throws \Exception
     */
    static public function render($model_class_name, $creation_form_html, $columns_config_arr, $context_arr = array())
    {

        // TODO: transactions??

        /*
        Operations::matchOperation(self::OPERATION_ADD_MODEL, function() use($model_class_name) {
            self::addModelOperation($model_class_name);
        });
        */

        Operations::matchOperation(self::OPERATION_DELETE_MODEL, function() use($model_class_name) {
            self::deleteModelOperation($model_class_name);
        });

        //
        // готовим список ID объектов для вывода
        //

        $filter = '';
        /* TODO
        if (isset($_GET['filter'])){
            $filter = $_GET['filter'];
        }
        */

        $objs_ids_arr = self::getObjIdsArrForClassName($model_class_name, $context_arr, $filter);

        //
        // вывод таблицы
        //

        // LIST TOOLBAR
        echo '<div>';

        if ($creation_form_html){
            $collapse_id = 'collapse_' . rand(0, 999999); // to enable multiple forms on one page
            echo '<div><a class="btn btn-default" role="button" data-toggle="collapse" href="#' . $collapse_id . '" aria-expanded="false">форма создания</a></div>';

            echo '<div class="collapse" id="' . $collapse_id . '">';

            echo  $creation_form_html;

            echo '</div>';
        }

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

        echo '<table class="table table-hover">';
        echo '<thead>';
        echo '<tr>';

            foreach ($columns_config_arr as $column_config) {
                $col_title = CRUDConfigReader::getOptionalSubkey($column_config, 'COLUMN_TITLE', '');
                echo '<th>' . Sanitize::sanitizeTagContent($col_title) . '</th>';
            }

        echo '<th>';
        echo '</th>';
        echo '</tr>';
        echo '</thead>';

        echo '<tbody>';

        foreach ($objs_ids_arr as $obj_id) {
            $obj_obj = ObjectLoader::createAndLoadObject($model_class_name, $obj_id);

            echo '<tr>';

            foreach ($columns_config_arr as $column_config){
                echo '<td>';

                $widget_config_arr = CRUDConfigReader::getRequiredSubkey($column_config, 'WIDGET');
                echo CRUDWidgets::renderListWidget($widget_config_arr, $obj_obj);

                echo '</td>';

            }

            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';

        echo Pager::renderPager(count($objs_ids_arr));
    }

    /**
     * Возвращает одну страницу списка объектов указанного класса.
     * Сортировка: TODO.
     * Фильтры: массив $context_arr.
     * Как определяется страница: см. Pager.
     * @param $model_class_name Имя класса модели
     * @param $context_arr array Массив пар "имя поля" - "значение поля"
     * @return array Массив идентикаторов объектов.
     */
    static public function getObjIdsArrForClassName($model_class_name, $context_arr, $title_filter = '')
    {
        \OLOG\Model\Helper::exceptionIfClassNotImplementsInterface($model_class_name, \OLOG\Model\InterfaceLoad::class);

        $page_size = Pager::getPageSize();
        $start = Pager::getPageOffset();

        $db_table_name = $model_class_name::DB_TABLE_NAME;
        $db_id = $model_class_name::DB_ID;

        $db_id_field_name = FieldsAccess::getIdFieldName($model_class_name);

        // selecting ids by params from context
        $query_param_values_arr = array();

        // TODO
        // внести в контекст кроме имени поля и значения еще и оператор, чтобы можно было делать поиск лайком через
        // контекст, а не отдельный параметр

        $where = ' 1 = 1 ';
        foreach ($context_arr as $column_name => $value) {
            // чистим имя поля, возможно пришедшее из запроса
            $column_name = preg_replace("/[^a-zA-Z0-9_]+/", "", $column_name);

            $where .= ' and ' . $column_name . ' = ?';
            $query_param_values_arr[] = $value;
        }

        /* TODO
        if (isset($model_class_name::$crud_model_title_field)){
            $title_field_name = $model_class_name::$crud_model_title_field;
            if ($title_filter != ''){
                $where .= ' and ' . $title_field_name . ' like ?';
                $query_param_values_arr[] = '%' . $title_filter . '%';
            }
        }
        */

        $order_field_name = $db_id_field_name;

        $obj_ids_arr = \OLOG\DB\DBWrapper::readColumn(
            $db_id,
            "select " . $db_id_field_name . " from " . $db_table_name . ' where ' . $where . ' order by ' . $order_field_name . ' desc limit ' . intval($page_size) . ' offset ' . intval($start),
            $query_param_values_arr
        );

        return $obj_ids_arr;

    }
}