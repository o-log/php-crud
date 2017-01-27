<?php

namespace OLOG\CRUD;

use OLOG\Assert;
use OLOG\DB\DBWrapper;
use OLOG\HTML;
use OLOG\MagnificPopup;
use OLOG\Model\InterfaceWeight;
use OLOG\Operations;
use OLOG\POSTAccess;
use OLOG\Redirects;
use OLOG\REQUESTWrapper;
use OLOG\Sanitize;
use OLOG\Url;

class CRUDTable
{
	const KEY_LIST_COLUMNS = 'LIST_COLUMNS';

	const OPERATION_ADD_MODEL = 'OPERATION_ADD_MODEL';
    const OPERATION_DELETE_MODEL = 'OPERATION_DELETE_MODEL';
    const OPERATION_SWAP_MODEL_WEIGHT = 'OPERATION_SWAP_MODEL_WEIGHT';
    const OPERATION_UPDATE_MODEL_FIELD = 'OPERATION_UPDATE_MODEL_FIELD';

    const FILTERS_POSITION_LEFT = 'FILTERS_POSITION_LEFT';
    const FILTERS_POSITION_RIGHT = 'FILTERS_POSITION_RIGHT';
    const FILTERS_POSITION_TOP = 'FILTERS_POSITION_TOP';
    const FILTERS_POSITION_NONE = 'FILTERS_POSITION_NONE';
	const FILTERS_POSITION_INLINE = 'FILTERS_POSITION_INLINE';

    const FIELD_CRUDTABLE_ID = 'crudtable_id';
    const FIELD_FIELD_NAME = 'field_name';
    const FIELD_FIELD_VALUE = 'field_value';
    const FIELD_MODEL_ID = 'model_id';

    static protected function deleteModelOperation()
    {
        // TODO: do not pass DB table name in form - pass crud table id instead, get model class name from crud table
        // TODO: also check model owner
        $model_class_name = POSTAccess::getRequiredPostValue(CRUDTableWidgetDelete::FIELD_CLASS_NAME);
        \OLOG\CheckClassInterfaces::exceptionIfClassNotImplementsInterface($model_class_name, \OLOG\Model\InterfaceDelete::class);

        $model_id = POSTAccess::getRequiredPostValue(CRUDTableWidgetDelete::FIELD_OBJECT_ID);

        $obj = CRUDObjectLoader::createAndLoadObject($model_class_name, $model_id);
        $obj->delete();

        $redirect_url = POSTAccess::getOptionalPostValue(CRUDTableWidgetDelete::FIELD_REDIRECT_AFTER_DELETE_URL, '');

        if ($redirect_url != ''){
            Redirects::redirect($redirect_url);
        }

        \OLOG\Redirects::redirectToSelf();
    }

    static protected function swapModelWeightOperation()
    {
        // TODO: do not pass DB table name in form - pass crud table id instead, get model class name from crud table
        // TODO: also check model owner
        $model_class_name = POSTAccess::getRequiredPostValue('_class_name'); // TODO: constant for field name
        \OLOG\CheckClassInterfaces::exceptionIfClassNotImplementsInterface($model_class_name, \OLOG\Model\InterfaceWeight::class);

        $model_id = POSTAccess::getRequiredPostValue('_id'); // TODO: constant for field name

        $context_fields_names_str = POSTAccess::getOptionalPostValue(CRUDTableWidgetWeight::FORMFIELD_CONTEXT_FIELDS_NAME, '');
        $context_fields_names_arr = [];
        if ($context_fields_names_str != '') {
            $context_fields_names_arr = explode(',', $context_fields_names_str);
        }

        $context_arr = [];
        foreach ($context_fields_names_arr as $context_field_name){
            $context_arr[$context_field_name] = NullablePostFields::optionalFieldValue($context_field_name);
        }

        /** @var InterfaceWeight $obj */
        $obj = CRUDObjectLoader::createAndLoadObject($model_class_name, $model_id);
        $obj->swapWeights($context_arr);

        \OLOG\Redirects::redirectToSelf();
    }

    static protected function updateModelFieldOperation($table_id, $model_class_name)
    {
        $table_id_from_request = REQUESTWrapper::optionalFieldValue(self::FIELD_CRUDTABLE_ID, '');

        // проверяем, что операция выполняется для таблицы из запроса, потому что класс модели мы берем из таблицы
        if ($table_id_from_request != $table_id){
            return;
        }

        $model_field_name = REQUESTWrapper::requiredFieldValue(self::FIELD_FIELD_NAME);
        $value = REQUESTWrapper::requiredFieldValue(self::FIELD_FIELD_VALUE);
        $model_id = REQUESTWrapper::requiredFieldValue(self::FIELD_MODEL_ID);

        // TODO: owner check!!!

        /*
        $db_table_name = $model_class_name::DB_TABLE_NAME; // TODO: check constant availability
        DBWrapper::query(
            $model_class_name::DB_ID, // check class availability
            'update ' . Sanitize::sanitizeSqlColumnName($db_table_name) . ' set ' . Sanitize::sanitizeSqlColumnName($db_table_field) . ' = ? where id = ?',
            [$value, $model_id]
        );
        */

        \OLOG\CheckClassInterfaces::exceptionIfClassNotImplementsInterface($model_class_name, \OLOG\Model\InterfaceSave::class);

        $obj = CRUDObjectLoader::createAndLoadObject($model_class_name, $model_id);

        $reflect = new \ReflectionClass($obj);

        $property_obj = $reflect->getProperty($model_field_name);
        $property_obj->setAccessible(true);
        $property_obj->setValue($obj, $value);

        $obj->save();
    }

    static protected function filterFormFieldName($table_id, $filter_index){
		return 'table_' . $table_id . '_filter_' . $filter_index;
	}

    static public function executeOperations($table_id, $model_class_name){
        static $__operations_executed = false;

        if ($__operations_executed){
            return;
        }

        $__operations_executed = true;

        Operations::matchOperation(self::OPERATION_DELETE_MODEL, function () {
            self::deleteModelOperation();
        });

        Operations::matchOperation(self::OPERATION_SWAP_MODEL_WEIGHT, function () {
            self::swapModelWeightOperation();
        });

        Operations::matchOperation(self::OPERATION_UPDATE_MODEL_FIELD, function () use ($table_id,  $model_class_name) {
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
	static public function html($model_class_name, $create_form_html, $column_obj_arr, $filters_arr = [], $order_by = '', $table_id = '', $filters_position = self::FILTERS_POSITION_NONE, $display_total_rows_count = false)
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

		$html = HTML::div($table_container_element_id, '', function() use ($model_class_name, $create_form_html, $column_obj_arr, $filters_arr, $order_by, $table_id, $filters_position, $display_total_rows_count) {

			echo '<div class="row">';

			if ($filters_position == self::FILTERS_POSITION_LEFT) {
				echo '<div class="col-sm-4">';
				echo self::filtersHtml($table_id, $filters_arr);
				echo '</div>';
			}

			$col_sm_class = '12';
			if (($filters_position == self::FILTERS_POSITION_LEFT) || ($filters_position == self::FILTERS_POSITION_RIGHT)) {
				$col_sm_class = '8';
			}
			echo '<div class="col-sm-' . $col_sm_class . '">';

            if ($filters_position != self::FILTERS_POSITION_INLINE) {
                echo self::toolbarHtml($table_id, $create_form_html, $filters_arr);
            }

			if ($filters_position == self::FILTERS_POSITION_TOP) {
				echo self::filtersHtml($table_id, $filters_arr);
			}

			if ($filters_position == self::FILTERS_POSITION_INLINE) {
				echo self::filtersAndCreateButtonHtmlInline($table_id, $filters_arr, $create_form_html);
			}

			echo '<table class="table table-hover">';

			/** @var InterfaceCRUDTableColumn $column_obj */
			$has_nonempty_th = false;
            foreach ($column_obj_arr as $column_obj) {
                Assert::assert($column_obj instanceof InterfaceCRUDTableColumn);
                if ($column_obj->getTitle() != ''){
                    $has_nonempty_th = true;
                }
            }

            if ($has_nonempty_th) {
                echo '<thead><tr>';
                foreach ($column_obj_arr as $column_obj) {
                    Assert::assert($column_obj instanceof InterfaceCRUDTableColumn);
                    echo '<th>' . $column_obj->getTitle() . '</th>';
                }
                echo '</tr></thead>';
            }

			echo '<tbody>';

            $total_rows_count = 0;
			$objs_ids_arr = CRUDInternalTableObjectsSelector::getObjIdsArrForClassName($table_id, $model_class_name, $filters_arr, $order_by, $display_total_rows_count, $total_rows_count);

			foreach ($objs_ids_arr as $obj_id) {
				$obj_obj = CRUDObjectLoader::createAndLoadObject($model_class_name, $obj_id);

				/** @var InterfaceCRUDTableColumn $column_obj */
				echo '<tr>';
				foreach ($column_obj_arr as $column_obj) {
					Assert::assert($column_obj instanceof InterfaceCRUDTableColumn);
				    /** @var InterfaceCRUDTableWidget $widget_obj */
				    $widget_obj = $column_obj->getWidgetObj();
                    Assert::assert($widget_obj);
                    Assert::assert($widget_obj instanceof InterfaceCRUDTableWidget);

                    $col_width_attr = '';

                    if ($widget_obj instanceof CRUDTableWidgetDelete){
                        $col_width_attr = ' width="1px" ';
                    }

                    if ($widget_obj instanceof CRUDTableWidgetWeight){
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

			echo Pager::renderPager($table_id, count($objs_ids_arr), $display_total_rows_count, $total_rows_count);

			echo '</div>';

			if ($filters_position == self::FILTERS_POSITION_RIGHT) {
				echo '<div class="col-sm-4">';
				echo self::filtersHtml($table_id, $filters_arr);
				echo '</div>';
			}

			echo '</div>';
		});

		// Загрузка скриптов
		$html .= CRUDTableScript::getHtml($table_container_element_id, Url::getCurrentUrlNoGetForm());

		return $html;
	}

    static protected function filtersHtml($table_index_on_page, $filters_arr)
    {
        $html = '';

        if ($filters_arr) {
            $html .= '<div class="">';
            $html .= '<form class="filters-form form-horizontal">';
            $html .= '<div class="row">';

            //$filter_index = 0;

            foreach ($filters_arr as $filter_obj){
                if ($filter_obj instanceof InterfaceCRUDTableFilter2) {
                    $html .= '<div class="col-md-12">';
                    $html .= '<div class="form-group">';

                    $html .= '<label class="col-sm-4 text-right control-label">' . $filter_obj->getTitle() . '</label>';
                    $html .= '<div class="col-sm-8">' . $filter_obj->getHtml() . '</div>';

                    $html .= '</div>';
                    $html .= '</div>';
                } elseif ($filter_obj instanceof InterfaceCRUDTableFilterInvisible) {
                    // do nothing with invisible filters
                } else {
                    throw new \Exception('filter doesnt implement interface ...');
                }
            }

            $html .= '</div>';
            $html .= '</form>';
            $html .= '</div>';
        }

        return $html;
    }

    static public function filtersAndCreateButtonHtmlInline($table_index_on_page, $filters_arr, $create_form_html = '')
    {
	    if (empty($filters_arr) && ($create_form_html == '')) {
		    return '';
	    }

	    $html = HTML::div('filters-inline', '', function () use ($table_index_on_page, $filters_arr, $create_form_html) {
            if (!empty($filters_arr)) {
                echo '<form class="filters-form" style="display: inline;">';

                foreach ($filters_arr as $filter_obj) {
                    if ($filter_obj instanceof InterfaceCRUDTableFilter2) {
                        echo '<div style="display: inline-block;margin-right: 10px;">';

                        if ($filter_obj->getTitle()) {
                            echo '<span style="display: inline-block;margin-right: 5px;">' . $filter_obj->getTitle() . '</span>';
                        }

                        echo '<span style="display: inline-block;">' . $filter_obj->getHtml() . '</span>';
                        echo '</div>';
                    } elseif ($filter_obj instanceof InterfaceCRUDTableFilterInvisible) {
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
                //$html .= '<button type="button" class="btn btn-default" data-toggle="modal" data-target="#' . $create_form_element_id . '">Создать</button>';
                //echo '<button type="button" class="btn btn-sm btn-default" data-toggle="collapse" data-target="#' . $create_form_element_id . '">Создать</button>';
                echo MagnificPopup::button($create_form_element_id, 'btn btn-sm btn-default pull-right', 'Создать');
                //}
                //echo '</span>';

                //if ($create_form_html) {
                //$html .= BT::modal($create_form_element_id, 'Форма создания', $create_form_html);
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
            //$html .= '<button type="button" class="btn btn-default" data-toggle="modal" data-target="#' . $create_form_element_id . '">Создать</button>';
            $html .= '<button type="button" class="btn btn-default" data-toggle="collapse" data-target="#' . $create_form_element_id . '">Создать</button>';
        }
        $html .= '</div>';

        if ($create_form_html) {
            //$html .= BT::modal($create_form_element_id, 'Форма создания', $create_form_html);
            $html .= '<div class="collapse" id="' . $create_form_element_id . '"><div class="well">' . $create_form_html . '</div></div>';
        }

        return $html;
    }

}