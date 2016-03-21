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

    static protected function deleteModelOperation($model_class_name)
    {

        // TODO: transactions??

        \OLOG\Model\Helper::exceptionIfClassNotImplementsInterface($model_class_name, \OLOG\Model\InterfaceDelete::class);

        $model_class_name = POSTAccess::getRequiredPostValue('_class_name'); // TODO: constant for field name
        $model_id = POSTAccess::getRequiredPostValue('_id'); // TODO: constant for field name

        $obj = CRUDObjectLoader::createAndLoadObject($model_class_name, $model_id);
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
    static public function html($model_class_name, $create_form_html, $column_obj_arr, $filters_arr = [])
    {
        Operations::matchOperation(self::OPERATION_DELETE_MODEL, function () use ($model_class_name) {
            self::deleteModelOperation($model_class_name);
        });

        //
        // готовим список ID объектов для вывода
        //

        $objs_ids_arr = self::getObjIdsArrForClassName($model_class_name, $filters_arr);

        //
        // вывод таблицы
        //

        $html = '';

        $html .= self::toolbarHtml($create_form_html, $filters_arr);

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
            $obj_obj = CRUDObjectLoader::createAndLoadObject($model_class_name, $obj_id);

            $html .= '<tr>';

            foreach ($column_obj_arr as $column_obj) {

                // TODO: check column_obj intaerfaceCol

                $html .= '<td>';

                $widget_obj = $column_obj->getWidgetObj();
                Assert::assert($widget_obj);

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

    static protected function toolbarHtml($create_form_html, $filters_arr)
    {
        $html = '';

        $create_form_element_id = 'collapse_' . rand(1, 999999);
        $filters_element_id = 'collapse_' . rand(1, 999999);

        $html .= '<div class="btn-group" role="group">';
        if ($create_form_html) {
            $html .= '<button class="btn btn-default" type="button" data-toggle="collapse" href="#' . $create_form_element_id . '">Форма создания</button>';
        }
        if ($filters_arr) {
            $html .= '<button class="btn btn-default" type="button" data-toggle="collapse" href="#' . $filters_element_id . '">Фильтры</button>';
        }
        $html .= '</div>';

        if ($create_form_html) {
            $html .= '<div class="collapse" id="' . $create_form_element_id . '">';
            $html .= '<div class="well">';

            $html .= $create_form_html;

            $html .= '</div>';
            $html .= '</div>';
        }

        if ($filters_arr) {
            $html .= '<div class="collapse" id="' . $filters_element_id . '">';
            $html .= '<div class="well">';

            //$html .= $create_form_html;
            /** @var CRUDTableFilter $filter_obj */
            foreach ($filters_arr as $filter_obj){
                // TODO: finish
                $html .= '<div>' . $filter_obj->getFieldName() . ': ' . $filter_obj->getValue() . '</div>';
            }

            $html .= '</div>';
            $html .= '</div>';
        }

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
    static public function getObjIdsArrForClassName($model_class_name, $filters_arr)
    {
        \OLOG\Model\Helper::exceptionIfClassNotImplementsInterface($model_class_name, \OLOG\Model\InterfaceLoad::class);

        $page_size = Pager::getPageSize();
        $start = Pager::getPageOffset();

        $db_table_name = $model_class_name::DB_TABLE_NAME;
        $db_id = $model_class_name::DB_ID;

        $db_id_field_name = CRUDFieldsAccess::getIdFieldName($model_class_name);

        // selecting ids by params from context
        $query_param_values_arr = array();

        // TODO
        // внести в контекст кроме имени поля и значения еще и оператор, чтобы можно было делать поиск лайком через
        // контекст, а не отдельный параметр

        $where = ' 1 = 1 ';

        /** @var CRUDTableFilter $filter_obj */
        foreach ($filters_arr as $filter_obj) {
            // TODO: check filter interface

            $column_name = $filter_obj->getFieldName();
            $operation_code = $filter_obj->getOperationCode();
            $value = $filter_obj->getValue();

            $column_name = preg_replace("/[^a-zA-Z0-9_]+/", "", $column_name);

            switch ($operation_code) {
                case CRUDTableFilter::FILTER_EQUAL:
                    $where .= ' and ' . $column_name . ' = ?';
                    $query_param_values_arr[] = $value;
                    break;

                case CRUDTableFilter::FILTER_IS_NULL:
                    $where .= ' and ' . $column_name . ' is null ';
                    break;

                default:
                    throw new \Exception('unknown filter code');
            }
        }

        $order_field_name = $db_id_field_name;

        $obj_ids_arr = \OLOG\DB\DBWrapper::readColumn(
            $db_id,
            "select " . $db_id_field_name . " from " . $db_table_name . ' where ' . $where . ' order by ' . $order_field_name . ' desc limit ' . intval($page_size) . ' offset ' . intval($start),
            $query_param_values_arr
        );

        return $obj_ids_arr;

    }
}