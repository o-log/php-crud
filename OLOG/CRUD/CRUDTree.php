<?php

namespace OLOG\CRUD;

use OLOG\Assert;
use OLOG\BT\BT;
use OLOG\GETAccess;
use OLOG\Model\InterfaceWeight;
use OLOG\Operations;
use OLOG\POSTAccess;
use OLOG\Preloader;
use OLOG\Redirects;
use OLOG\Render;
use OLOG\Sanitize;
use OLOG\Url;

class CRUDTree
{
    static public function html($model_class_name, $create_form_html, $column_obj_arr, $parent_id_field_name, $order_by = '', $table_id = '1', $filters_arr = [], $col_with_padding_index = 0, $filters_position = CRUDTable::FILTERS_POSITION_NONE)
    {

        // TODO: придумать способ автогенерации table_id, который был бы уникальным, но при этом один и тот же когда одну таблицу запрашиваешь несколько раз

        static $CRUDTree_include_script;

        CRUDTable::executeOperations();

        $script = '';

        // include script only once per page
        if(!isset($CRUDTree_include_script)){
            $script = '<script src="//cdnjs.cloudflare.com/ajax/libs/js-url/2.3.0/url.min.js"></script>';
            $script .= '<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>';
            $script .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css">';

	        $script .= Preloader::preloaderJsHtml();
            $script .= '<script>';
            $script .= Render::callLocaltemplate('templates/crudtable.js');
            $script .= '</script>';

            $CRUDTree_include_script = false;
        }

        $objs_ids_arr = CRUDInternalTableObjectsSelector::getRecursiveObjIdsArrForClassName($model_class_name, $parent_id_field_name, $filters_arr, $order_by);

        //
        // вывод таблицы
        //

        $table_container_element_id = 'tableContainer_' . $table_id;

        // оборачиваем в отдельный div для выдачи только таблицы аяксом - иначе корневой элемент документа не будет доступен в jquery селекторах
        $html = '<div>';
        $html .= '<div class="' . $table_container_element_id . ' row">';

        if ($filters_position == CRUDTable::FILTERS_POSITION_LEFT) {
            $html .= '<div class="col-sm-4">';
            $html .= self::filtersHtml($filters_arr);
            $html .= '</div>';
            $html .= '<div class="col-sm-8">';
        } else {
            $html .= '<div class="col-sm-12">';
        }

        $html .= self::toolbarHtml($table_id, $create_form_html);

        if ($filters_position == CRUDTable::FILTERS_POSITION_TOP) {
            $html .= self::filtersHtml($filters_arr);
        }

        $html .= '<table class="table table-hover">';
        $html .= '<thead>';
        $html .= '<tr>';

        /** @var InterfaceCRUDTableColumn $column_obj */
        foreach ($column_obj_arr as $column_obj) {
            Assert::assert($column_obj instanceof InterfaceCRUDTableColumn);
            $html .= '<th>' . Sanitize::sanitizeTagContent($column_obj->getTitle()) . '</th>';
        }

        $html .= '</tr>';
        $html .= '</thead>';

        $html .= '<tbody>';

        foreach ($objs_ids_arr as $obj_data) {
            $obj_id = $obj_data['id'];
            $obj_obj = CRUDObjectLoader::createAndLoadObject($model_class_name, $obj_id);

            $html .= '<tr>';

            /** @var InterfaceCRUDTableColumn $column_obj */
            foreach ($column_obj_arr as $col_index => $column_obj) {
                Assert::assert($column_obj instanceof InterfaceCRUDTableColumn);

                $html .= '<td>';

                if ($col_index == $col_with_padding_index){
                    $html .= '<div style="padding-left: ' . ($obj_data['depth'] * 30) . 'px;">';
                }

                /** @var InterfaceCRUDTableWidget $widget_obj */
                $widget_obj = $column_obj->getWidgetObj();

                Assert::assert($widget_obj);
                Assert::assert($widget_obj instanceof InterfaceCRUDTableWidget);

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

        $html .= '<script>CRUD.Table.init("' . $table_container_element_id . '", "' . Url::getCurrentUrlNoGetForm() . '");</script>';

        return $script . $html;
    }

    static protected function filtersHtml($filters_arr)
    {
        $html = '';

        if ($filters_arr) {
            $html .= '<div class="">';
            $html .= '<form class="filters-form form-horizontal">';
            $html .= '<div class="row">';

            /** @var InterfaceCRUDTableFilter2 $filter_obj */
            foreach ($filters_arr as $filter_obj){
                Assert::assert($filter_obj instanceof InterfaceCRUDTableFilter2);

                $html .= '<div class="col-md-12">';
                $html .= '<div class="form-group">';

                $html .= '<label class="col-sm-4 text-right control-label">' . $filter_obj->getTitle() . '</label>';
                $html .= '<div class="col-sm-8">' . $filter_obj->getHtml() . '</div>';

                $html .= '</div>';
                $html .= '</div>';
            }

            $html .= '</div>';
            //$html .= '<div class="row"><div class="col-sm-8 col-sm-offset-4"><button style="width: 100%;" type="submit" class="btn btn-default">Поиск</button></div></div>';
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
            $html .= '<a href="#' . $create_form_element_id . '" class="btn btn-default open-' . $create_form_element_id . '">CREATE</a>';

            $html .= '<script>
                $(".open-' . $create_form_element_id . '").magnificPopup({
                    type: "inline",
                    midClick: true // allow opening popup on middle mouse click. Always set it to true if you don\'t provide alternative source.
                    });
                </script>';
        }
        $html .= '</div>';

        if ($create_form_html) {
            $html .= '<div style="position: relative; background: #FFF; padding: 50px 20px 30px 20px; width: auto; max-width: 700px; margin: 20px auto;" id="' . $create_form_element_id . '" class="mfp-hide">';
            $html .= $create_form_html;
            $html .= '</div>';
        }

        return $html;
    }

}