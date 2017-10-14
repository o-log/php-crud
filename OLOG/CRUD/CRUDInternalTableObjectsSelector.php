<?php

namespace OLOG\CRUD;



class CRUDInternalTableObjectsSelector
{
    static public function getRecursiveObjIdsArrForClassName($model_class_name, $parent_id_field_name, $filters_arr, $order_by = '', $parent_id = null, $depth = 0)
    {
        if(!is_a($model_class_name, \OLOG\Model\ActiveRecordInterface::class, true)){
            throw new \Exception();
        }

        $db_table_name = $model_class_name::DB_TABLE_NAME;
        $db_id = $model_class_name::DB_ID;

        $db_id_field_name = CRUDFieldsAccess::getIdFieldName($model_class_name);

        $query_param_values_arr = array();

        $where = ' 1 = 1 ';

        if (is_null($parent_id)){
            $where .= ' and ' . $parent_id_field_name . ' is null ';
        } else {
            $where .= ' and ' . $parent_id_field_name . ' = ? ';
            $query_param_values_arr[] = $parent_id;
        }

        /** @var InterfaceCRUDTableFilter2 $filter_obj */
        foreach ($filters_arr as $filter_obj) {
            /*
            assert($filter_obj instanceof InterfaceCRUDTableFilter2);

            list($filter_sql_condition, $filter_placeholder_values_arr) = $filter_obj->sqlConditionAndPlaceholderValue();
            if ($filter_sql_condition != ''){
                $where .= ' and ' . $filter_sql_condition;
            }

            $query_param_values_arr = array_merge($query_param_values_arr, $filter_placeholder_values_arr);
            */
            if ($filter_obj instanceof InterfaceCRUDTableFilter2) {
                list($filter_sql_condition, $filter_placeholder_values_arr) = $filter_obj->sqlConditionAndPlaceholderValue();
                if ($filter_sql_condition != ''){
                    $where .= ' and ' . $filter_sql_condition;
                }

                $query_param_values_arr = array_merge($query_param_values_arr, $filter_placeholder_values_arr);
            } elseif ($filter_obj instanceof InterfaceCRUDTableFilterInvisible) {
                list($filter_sql_condition, $filter_placeholder_values_arr) = $filter_obj->sqlConditionAndPlaceholderValue();
                if ($filter_sql_condition != ''){
                    $where .= ' and ' . $filter_sql_condition;
                }

                $query_param_values_arr = array_merge($query_param_values_arr, $filter_placeholder_values_arr);
            } else {
                throw new \Exception('filter doesnt implement InterfaceCRUDTableFilter nor InterfaceCRUDTableFilter2');
            }
        }

        if ($order_by == '') {
            $order_by = $db_id_field_name;
        }

        $obj_ids_arr = \OLOG\DB\DB::readColumn(
            $db_id,
            "select " . $db_id_field_name . " from " . $db_table_name . ' where ' . $where . ' order by ' . $order_by,
            $query_param_values_arr
        );

        $output_arr = [];

        foreach ($obj_ids_arr as $fetched_obj_id){
            $output_arr[] = ['id' => $fetched_obj_id, 'depth' => $depth];
            $output_arr = array_merge($output_arr, self::getRecursiveObjIdsArrForClassName($model_class_name, $parent_id_field_name, $filters_arr, $order_by, $fetched_obj_id, $depth + 1));
        }

        return $output_arr;
    }

    /**
     * Возвращает одну страницу списка объектов указанного класса.
     * Сортировка: TODO.
     * Фильтры: массив $context_arr.
     * Как определяется страница: см. Pager.
     * @param $model_class_name string Имя класса модели
     * @param $context_arr array Массив пар "имя поля" - "значение поля"
     * @return array Массив идентикаторов объектов.
     */
    static public function getObjIdsArrForClassName($table_index_on_page, $model_class_name, $filters_arr, $order_by = '', $execute_total_rows_count_query = false, &$total_rows_count = 0)
    {
        if (!is_a($model_class_name, \OLOG\Model\ActiveRecordInterface::class, true)){
            throw new \Exception();
        }

        $page_size = Pager::getPageSize($table_index_on_page);
        $start = Pager::getPageOffset($table_index_on_page);

        $db_table_name = $model_class_name::DB_TABLE_NAME;
        $db_id = $model_class_name::DB_ID;

        $db_id_field_name = CRUDFieldsAccess::getIdFieldName($model_class_name);

        $query_param_values_arr = array();

        $where = ' 1 = 1 ';

        foreach ($filters_arr as $filter_obj) {
            if ($filter_obj instanceof InterfaceCRUDTableFilter2) {
                list($filter_sql_condition, $filter_placeholder_values_arr) = $filter_obj->sqlConditionAndPlaceholderValue();
                if ($filter_sql_condition != ''){
                    $where .= ' and ' . $filter_sql_condition;
                }

                $query_param_values_arr = array_merge($query_param_values_arr, $filter_placeholder_values_arr);
            } elseif ($filter_obj instanceof InterfaceCRUDTableFilterInvisible) {
                list($filter_sql_condition, $filter_placeholder_values_arr) = $filter_obj->sqlConditionAndPlaceholderValue();
                if ($filter_sql_condition != ''){
                    $where .= ' and ' . $filter_sql_condition;
                }

                $query_param_values_arr = array_merge($query_param_values_arr, $filter_placeholder_values_arr);
            } else {
                throw new \Exception('filter doesnt implement InterfaceCRUDTableFilter nor InterfaceCRUDTableFilter2');
            }
        }

        if ($order_by == '') {
            $order_by = $db_id_field_name;
        }

        $obj_ids_arr = \OLOG\DB\DB::readColumn(
            $db_id,
            'select ' . $db_id_field_name . ' from ' . $db_table_name . ' where ' . $where . ' order by ' . $order_by . ' limit ' . intval($page_size) . ' offset ' . intval($start),
            $query_param_values_arr
        );

        if ($execute_total_rows_count_query){
            $total_rows_count = \OLOG\DB\DB::readField(
                $db_id,
                'select count(*) from ' . $db_table_name . ' where ' . $where,
                $query_param_values_arr
            );
        }

        return $obj_ids_arr;
    }
}