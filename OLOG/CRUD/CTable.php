<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

use OLOG\BT\BT4;
use OLOG\HTML;
use OLOG\MagnificPopup;
use OLOG\Model\WeightInterface;
use OLOG\Form;
use OLOG\POST;
use OLOG\Redirects;
use OLOG\REQUEST;
use OLOG\Url;

class CTable
{
	const KEY_LIST_COLUMNS = 'LIST_COLUMNS';

	const OPERATION_ADD_MODEL = 'OPERATION_ADD_MODEL';
    const OPERATION_DELETE_MODEL = 'OPERATION_DELETE_MODEL';
    const OPERATION_SWAP_MODEL_WEIGHT = 'OPERATION_SWAP_MODEL_WEIGHT';
    const OPERATION_UPDATE_MODEL_FIELD = 'OPERATION_UPDATE_MODEL_FIELD';

    const FIELD_CRUDTABLE_ID = 'crudtable_id';
    const FIELD_FIELD_NAME = 'field_name';
    const FIELD_FIELD_VALUE = 'field_value';
    const FIELD_MODEL_ID = 'model_id';

    static protected function deleteModelOperation()
    {
        // TODO: do not pass DB table name in form - pass crud table id instead, get model class name from crud table
        // TODO: also check model owner
        $model_class_name = POST::required(TWDelete::FIELD_CLASS_NAME);
        if (!is_a($model_class_name, \OLOG\Model\ActiveRecordInterface::class, true)){
            throw new \Exception();
        }

        $model_id = POST::required(TWDelete::FIELD_OBJECT_ID);

        $obj = CInternalObjectLoader::createAndLoadObject($model_class_name, $model_id);
        $obj->delete();

        $redirect_url = POST::optional(TWDelete::FIELD_REDIRECT_AFTER_DELETE_URL, '');

        if ($redirect_url != ''){
            Redirects::redirect($redirect_url);
        }

        Redirects::redirectToSelf();
    }

    static protected function swapModelWeightOperation()
    {
        // TODO: do not pass DB table name in form - pass crud table id instead, get model class name from crud table
        // TODO: also check model owner
        $model_class_name = POST::required('_class_name'); // TODO: constant for field name
        if (!is_a($model_class_name, WeightInterface::class, true)){
            throw new \Exception();
        }

        $model_id = POST::required('_id'); // TODO: constant for field name

        $context_fields_names_str = POST::optional(TWWeight::FORMFIELD_CONTEXT_FIELDS_NAME, '');
        $context_fields_names_arr = [];
        if ($context_fields_names_str != '') {
            $context_fields_names_arr = explode(',', $context_fields_names_str);
        }

        $context_arr = [];
        foreach ($context_fields_names_arr as $context_field_name){
            $context_arr[$context_field_name] = NullablePostFields::optionalFieldValue($context_field_name);
        }

        /** @var WeightInterface $obj */
        $obj = CInternalObjectLoader::createAndLoadObject($model_class_name, $model_id);
        $obj->swapWeights($context_arr);

        Redirects::redirectToSelf();
    }

    static protected function updateModelFieldOperation($table_id, $model_class_name)
    {
        $table_id_from_request = REQUEST::optional(self::FIELD_CRUDTABLE_ID, '');

        // проверяем, что операция выполняется для таблицы из запроса, потому что класс модели мы берем из таблицы
        if ($table_id_from_request != $table_id){
            return;
        }

        $model_field_name = REQUEST::required(self::FIELD_FIELD_NAME);
        $value = REQUEST::required(self::FIELD_FIELD_VALUE);
        $model_id = REQUEST::required(self::FIELD_MODEL_ID);

        // TODO: owner check!!!

        /*
        $db_table_name = $model_class_name::DB_TABLE_NAME; // TODO: check constant availability
        DB::query(
            $model_class_name::DB_ID, // check class availability
            'update ' . Sanitize::sanitizeSqlColumnName($db_table_name) . ' set ' . Sanitize::sanitizeSqlColumnName($db_table_field) . ' = ? where id = ?',
            [$value, $model_id]
        );
        */

        if (!is_a($model_class_name, \OLOG\Model\ActiveRecordInterface::class, true)){
            throw new \Exception();
        }

        $obj = CInternalObjectLoader::createAndLoadObject($model_class_name, $model_id);

        $reflect = new \ReflectionClass($obj);

        $property_obj = $reflect->getProperty($model_field_name);
        $property_obj->setAccessible(true);
        $property_obj->setValue($obj, $value);

        $obj->save();
    }

    static protected function filterFormFieldName($table_id, $filter_index){
		return 'table_' . $table_id . '_filter_' . $filter_index;
	}

    static public function executeOperations($table_id = '', $model_class_name = ''){
        static $__operations_executed = false;

        if ($__operations_executed){
            return;
        }

        $__operations_executed = true;

        Form::match(self::OPERATION_DELETE_MODEL, function () {
            self::deleteModelOperation();
        });

        Form::match(self::OPERATION_SWAP_MODEL_WEIGHT, function () {
            self::swapModelWeightOperation();
        });

        Form::match(self::OPERATION_UPDATE_MODEL_FIELD, function () use ($table_id,  $model_class_name) {
            self::updateModelFieldOperation($table_id, $model_class_name);
        });
    }

	/**
     * table_id - это идентификатор таблицы на странице, к которому привязываются все данные: имена полей формы и т.п.
     * @param $model_class_name
	 * @param $create_form_html
	 * @param $column_obj_arr
	 * @param array $filters_arr
	 * @param string $order_by
	 * @return string
	 */
	static public function html($model_class_name, $create_form_html, $column_obj_arr, $filters_arr = [], $order_by = '', $table_id = '', $title = '', $display_total_rows_count = false, $default_page_size = 30)
	{

	    // TODO: придумать способ автогенерации table_id, который был бы уникальным, но при этом один и тот же когда одну таблицу запрашиваешь несколько раз
        self::executeOperations($table_id, $model_class_name);

		//
		// вывод таблицы
		//

		$table_container_element_id = uniqid('tableContainer_');
		if ($table_id) {
			$table_container_element_id = $table_id;
		}

        // оборачиваем в отдельный div для выдачи только таблицы аяксом - иначе корневой элемент документа не будет доступен в jquery селекторах

		$html = HTML::div($table_container_element_id, '', function() use ($model_class_name, $create_form_html, $column_obj_arr, $filters_arr, $order_by, $table_id, $display_total_rows_count, $title, $default_page_size) {
            //echo '<div>';
            //echo '<div>';

            echo self::filtersAndCreateButtonHtmlInline($table_id, $filters_arr, $create_form_html, $title);

            $total_rows_count = 0;
            $page_size = Pager::getPageSize($table_id, $default_page_size);
            $start = Pager::getPageOffset($table_id);
            $objs_ids_arr = CInternalTObjectsSelector::getObjIdsArrForClassName($table_id, $model_class_name, $filters_arr, $start, $page_size, $order_by, $display_total_rows_count, $total_rows_count);

            if (($start == 0) && (count($objs_ids_arr) == 0)) {
                // контенер с классом table здесь должен быть обязательно, инача js не сможет извлечь таблицу из выдачи
                echo '<table class="table table-hover table-sm"><tr><td>';
                BT4::card('', '<div class="fa fa-archive"></div><div>Нет записей</div>', ['text-center']);
                echo '</td></tr></table>';
            } else {
                echo '<table class="table table-hover table-sm">';

                /** @var TColInterface $column_obj */
                $has_nonempty_th = false;
                foreach ($column_obj_arr as $column_obj) {
                    assert($column_obj instanceof TColInterface);
                    if ($column_obj->getTitle() != '') {
                        $has_nonempty_th = true;
                    }
                }

                if ($has_nonempty_th) {
                    echo '<thead><tr>';
                    foreach ($column_obj_arr as $column_obj) {
                        assert($column_obj instanceof TColInterface);
                        echo '<th>' . $column_obj->getTitle() . '</th>';
                    }
                    echo '</tr></thead>';
                }

                echo '<tbody>';

                foreach ($objs_ids_arr as $obj_id) {
                    $obj_obj = CInternalObjectLoader::createAndLoadObject($model_class_name, $obj_id);

                    /** @var TColInterface $column_obj */
                    echo '<tr>';
                    foreach ($column_obj_arr as $column_obj) {
                        assert($column_obj instanceof TColInterface);
                        /** @var TWInterface $widget_obj */
                        $widget_obj = $column_obj->getWidgetObj();
                        assert($widget_obj);
                        assert($widget_obj instanceof TWInterface);

                        $col_width_attr = '';

                        if ($widget_obj instanceof TWDelete) {
                            $col_width_attr = ' width="1px" ';
                        }

                        if ($widget_obj instanceof TWWeight) {
                            $col_width_attr = ' width="1px" ';
                        }

                        echo '<td ' . $col_width_attr . ' style="word-break: break-all;">';
                        echo $widget_obj->html($obj_obj);
                        echo '</td>';

                    }
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';

                echo Pager::renderPager($table_id, count($objs_ids_arr), $display_total_rows_count, $total_rows_count, $default_page_size);
            }

            //echo '</div>';
            //echo '</div>';
		});

		// Загрузка скриптов
		$html .= CInternalTScript::getHtml($table_container_element_id, Url::path());

		return $html;
	}

    static public function filtersAndCreateButtonHtmlInline($table_index_on_page, $filters_arr, $create_form_html = '', $title = '')
    {
	    if (empty($filters_arr) && ($create_form_html == '')) {
		    return '';
	    }

	    $html = HTML::div('filters-inline clearfix mb-2', '', function () use ($table_index_on_page, $filters_arr, $create_form_html, $title) {
	        //echo '<style>.filters-inline {margin-bottom: 10px;}</style>';

            if ($title != ''){
                echo '<span class="font-weight-bold mr-3">' . $title . '</span>';
            }

            if (!empty($filters_arr)) {
                echo '<form class="filters-form form-inline" style="display: inline;">';

                foreach ($filters_arr as $filter_obj) {
                    if ($filter_obj instanceof TFInterface) {
                        echo '<div style="display: inline-block;margin-right: 10px;">';

                        if ($filter_obj->getTitle()) {
                            echo '<span style="display: inline-block;margin-right: 5px;">' . $filter_obj->getTitle() . '</span>';
                        }

                        echo '<span style="display: inline-block;">' . $filter_obj->getHtml() . '</span>';
                        echo '</div>';
                    } elseif ($filter_obj instanceof TFHiddenInterface) {
                        // do nothing with invisible filters
                    } else {
                        throw new \Exception('filter doesnt implement interface ...');
                    }
                }

                echo '</form>';
            }

            if ($create_form_html != ''){
                $create_form_element_id = 'collapse_' . rand(1, 999999);

                //echo '<span class="pull-right">';
                //if ($create_form_html) {
                //echo '<button type="button" class="btn btn-sm btn-secondary pull-right" data-toggle="modal" data-target="#' . $create_form_element_id . '"><i class="fa fa-plus"></i></button>';
                //echo '<button type="button" class="btn btn-sm btn-secondary" data-toggle="collapse" data-target="#' . $create_form_element_id . '">Создать</button>';
                echo MagnificPopup::button($create_form_element_id, 'btn btn-sm btn-secondary pull-right ', '<i class="fa fa-plus"></i>');
                //}
                //echo '</span>';

                //if ($create_form_html) {
                //BT::modal($create_form_element_id, 'Форма создания', function() use ($create_form_html){echo $create_form_html;});
                //echo '<div class="collapse" id="' . $create_form_element_id . '"><div class="well">' . $create_form_html . '</div></div>';
                echo MagnificPopup::popupHtml($create_form_element_id, $create_form_html);
                //}
            }
	    });

	    return $html;
    }

    static protected function toolbarHtml($table_index_on_page, $create_form_html, $filters_arr)
    {
        if ($create_form_html == ''){
            return '';
        }

        $html = '';

        $create_form_element_id = 'collapse_' . rand(1, 999999);

        $html .= '<div class="btn-group" role="group">';
        if ($create_form_html) {
            //$html .= '<button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#' . $create_form_element_id . '">Создать</button>';
            $html .= '<button type="button" class="btn btn-secondary" data-toggle="collapse" data-target="#' . $create_form_element_id . '">Создать</button>';
        }
        $html .= '</div>';

        if ($create_form_html) {
            //$html .= BT::modal($create_form_element_id, 'Форма создания', $create_form_html);
            $html .= '<div class="collapse" id="' . $create_form_element_id . '"><div class="well">' . $create_form_html . '</div></div>';
            $html .= '<script>$("#' . $create_form_element_id . '").on("shown.bs.collapse", function () {$(this).find(".form-control").eq(0).focus();})</script>';
        }

        return $html;
    }

}
