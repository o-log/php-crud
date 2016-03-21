<?php

namespace OLOG\CRUD;

use OLOG\Assert;
use OLOG\POSTAccess;
use OLOG\Sanitize;

class CRUDTable
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

    static protected function deleteModelOperation($model_class_name)
    {

        // TODO: transactions??

        \OLOG\Model\Helper::exceptionIfClassNotImplementsInterface($model_class_name, \OLOG\Model\InterfaceDelete::class);

        $model_class_name = POSTAccess::getRequiredPostValue('_class_name'); // TODO: constant for field name
        $model_id = POSTAccess::getRequiredPostValue('_id'); // TODO: constant for field name

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
    static public function html($model_class_name, $column_obj_arr, $context_arr = array())
    {
        Operations::matchOperation(self::OPERATION_DELETE_MODEL, function () use ($model_class_name) {
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

        /* TODO
        if (isset($model_class_name::$crud_model_title_field)) {
            if (isset($model_class_name::$crud_allow_search)) {
                if ($model_class_name::$crud_allow_search == true) {
                    echo '<div class="pull-right" style="margin-top: 25px;"><form action="' . \Sportbox\Helpers::uri_no_getform() . '"><input name="filter" value="' . $filter . '"><input type="submit" value="искать"></form></div>';
                }
            }
        }
        */

        $html = '';

        $html .= '<table class="table table-hover">';
        $html .= '<thead>';
        $html .= '<tr>';

        foreach ($column_obj_arr as $column_obj) {

            // TODO: check column_obj intaerfaceCol

            $html .= '<th>' . Sanitize::sanitizeTagContent($column_obj->getTitle()) . '</th>';
        }

        $html .= '</tr>';
        $html .= '</thead>';

        $html .= '<tbody>';

        foreach ($objs_ids_arr as $obj_id) {
            $obj_obj = ObjectLoader::createAndLoadObject($model_class_name, $obj_id);

            $html .= '<tr>';

            foreach ($column_obj_arr as $column_obj) {

                // TODO: check column_obj intaerfaceCol

                $html .= '<td>';

                $widget_obj = $column_obj->getWidgetObj();
                Assert::assert($widget_obj);
                //$html .= CRUDWidgets::renderListWidget($widget_config_arr, $obj_obj);

                // TODO: check widget obj interface

                $html .= $widget_obj->html($obj_obj);

                $html .= '</td>';

            }

            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        $html .= Pager::renderPager(count($objs_ids_arr));

        return $html;
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