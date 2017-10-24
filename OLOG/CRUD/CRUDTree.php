<?php

namespace OLOG\CRUD;


use OLOG\HTML;
use OLOG\Url;

class CRUDTree
{
    static public function html($model_class_name, $create_form_html, $column_obj_arr, $parent_id_field_name, $order_by = '', $table_id = '1', $filters_arr = [], $col_with_padding_index = 0, $filters_position = CTable::FILTERS_POSITION_NONE)
    {

        // TODO: придумать способ автогенерации table_id, который был бы уникальным, но при этом один и тот же когда одну таблицу запрашиваешь несколько раз
        CTable::executeOperations($table_id, $model_class_name);

        $objs_ids_arr = CInternalTObjectsSelector::getRecursiveObjIdsArrForClassName($model_class_name, $parent_id_field_name, $filters_arr, $order_by);

        //
        // вывод таблицы
        //

        $table_container_element_id = 'tableContainer_' . $table_id;

        // оборачиваем в отдельный div для выдачи только таблицы аяксом - иначе корневой элемент документа не будет доступен в jquery селекторах
        $html = '<div>';
        $html .= '<div class="' . $table_container_element_id . ' row">';

        if ($filters_position == CTable::FILTERS_POSITION_LEFT) {
            $html .= '<div class="col-sm-4">';
            $html .= self::filtersHtml($filters_arr);
            $html .= '</div>';
            $html .= '<div class="col-sm-8">';
        } else {
            $html .= '<div class="col-sm-12">';
        }

        if ($filters_position != CTable::FILTERS_POSITION_INLINE) {
            $html .= self::toolbarHtml($table_id, $create_form_html);
        }

        if ($filters_position == CTable::FILTERS_POSITION_TOP) {
            $html .= self::filtersHtml($filters_arr);
        }

        if ($filters_position == CTable::FILTERS_POSITION_INLINE) {
            $html .= CTable::filtersAndCreateButtonHtmlInline($table_id, $filters_arr, $create_form_html);
        }

        $html .= '<table class="table table-hover">';
        $html .= '<thead>';
        $html .= '<tr>';

        /** @var CColInterface $column_obj */
        foreach ($column_obj_arr as $column_obj) {
            assert($column_obj instanceof CColInterface);
            $html .= '<th>' . HTML::content($column_obj->getTitle()) . '</th>';
        }

        $html .= '</tr>';
        $html .= '</thead>';

        $html .= '<tbody>';

        foreach ($objs_ids_arr as $obj_data) {
            $obj_id = $obj_data['id'];
            $obj_obj = CInternalObjectLoader::createAndLoadObject($model_class_name, $obj_id);

            $html .= '<tr>';

            /** @var CColInterface $column_obj */
            foreach ($column_obj_arr as $col_index => $column_obj) {
                assert($column_obj instanceof CColInterface);

                /** @var TWInterface $widget_obj */
                $widget_obj = $column_obj->getWidgetObj();

                assert($widget_obj);
                assert($widget_obj instanceof TWInterface);

                $col_width_attr = '';

                if ($widget_obj instanceof TWDelete){
                    $col_width_attr = ' width="1px" ';
                }

                if ($widget_obj instanceof TWWeight){
                    $col_width_attr = ' width="1px" ';
                }

                $html .= '<td ' . $col_width_attr . '>';

                if ($col_index == $col_with_padding_index){
                    $html .= '<div style="padding-left: ' . ($obj_data['depth'] * 30) . 'px;">';
                }

                $html .= $widget_obj->html($obj_obj);

                if ($col_index == 0) {
                    $html .= '</div>';
                }

                $html .= '</td>';

            }

            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>';


	    // Загрузка скриптов
	    $html .= CInternalTScript::getHtml($table_container_element_id, Url::path());

        return $html;
    }

    static protected function filtersHtml($filters_arr)
    {
        $html = '';

        if ($filters_arr) {
            $html .= '<div class="">';
            $html .= '<form class="filters-form form-horizontal">';
            $html .= '<div class="row">';

            /** @var TF2Interface $filter_obj */
            foreach ($filters_arr as $filter_obj){
                assert($filter_obj instanceof TF2Interface);

                $html .= '<div class="col-md-12">';
                $html .= '<div class="form-group row">';

                $html .= '<label class="col-sm-4 text-right col-form-label">' . $filter_obj->getTitle() . '</label>';
                $html .= '<div class="col-sm-8">' . $filter_obj->getHtml() . '</div>';

                $html .= '</div>';
                $html .= '</div>';
            }

            $html .= '</div>';
            //$html .= '<div class="row"><div class="col-sm-8 col-sm-offset-4"><button style="width: 100%;" type="submit" class="btn btn-secondary">Поиск</button></div></div>';
            $html .= '</form>';
            $html .= '</div>';
        }

        return $html;
    }

    static protected function toolbarHtml($table_index_on_page, $create_form_html)
    {
        $html = '';

        $create_form_element_id = 'collapse_' . rand(1, 999999);

        $html .= '<div class="btn-group" role="group">';
        if ($create_form_html) {
            $html .= '<button type="button" class="btn btn-secondary" data-toggle="collapse" data-target="#' . $create_form_element_id . '">Создать</button>';

//            $html .= '<a href="#' . $create_form_element_id . '" class="btn btn-secondary open-' . $create_form_element_id . '">CREATE</a>';
//
//            $html .= '<script>
//                $(".open-' . $create_form_element_id . '").magnificPopup({
//                    type: "inline",
//                    midClick: true // allow opening popup on middle mouse click. Always set it to true if you don\'t provide alternative source.
//                    });
//                </script>';
        }
        $html .= '</div>';

        if ($create_form_html) {
            $html .= '<div class="collapse" id="' . $create_form_element_id . '"><div class="well">' . $create_form_html . '</div></div>';
            
//            $html .= '<div style="position: relative; background: #FFF; padding: 50px 20px 30px 20px; width: auto; max-width: 700px; margin: 20px auto;" id="' . $create_form_element_id . '" class="mfp-hide">';
//            $html .= $create_form_html;
//            $html .= '</div>';
        }

        return $html;
    }

}