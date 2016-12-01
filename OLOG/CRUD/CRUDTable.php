<?php

namespace OLOG\CRUD;

use OLOG\Assert;
use OLOG\BT\BT;
use OLOG\GETAccess;
use OLOG\HTML;
use OLOG\Model\InterfaceWeight;
use OLOG\Operations;
use OLOG\POSTAccess;
use OLOG\Preloader;
use OLOG\Redirects;
use OLOG\Render;
use OLOG\Sanitize;
use OLOG\Url;

class CRUDTable
{
	const KEY_LIST_COLUMNS = 'LIST_COLUMNS';
	const OPERATION_ADD_MODEL = 'OPERATION_ADD_MODEL';
    const OPERATION_DELETE_MODEL = 'OPERATION_DELETE_MODEL';
    const OPERATION_SWAP_MODEL_WEIGHT = 'OPERATION_SWAP_MODEL_WEIGHT';

    const FILTERS_POSITION_LEFT = 'FILTERS_POSITION_LEFT';
    const FILTERS_POSITION_RIGHT = 'FILTERS_POSITION_RIGHT';
    const FILTERS_POSITION_TOP = 'FILTERS_POSITION_TOP';
    const FILTERS_POSITION_NONE = 'FILTERS_POSITION_NONE';
	const FILTERS_POSITION_INLINE = 'FILTERS_POSITION_INLINE';

    static protected function deleteModelOperation()
    {
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
        $model_class_name = POSTAccess::getRequiredPostValue('_class_name'); // TODO: constant for field name
        \OLOG\CheckClassInterfaces::exceptionIfClassNotImplementsInterface($model_class_name, \OLOG\Model\InterfaceWeight::class);

        $model_id = POSTAccess::getRequiredPostValue('_id'); // TODO: constant for field name

        $context_fields_names_str = POSTAccess::getRequiredPostValue(CRUDTableWidgetWeight::FORMFIELD_CONTEXT_FIELDS_NAME);
        $context_fields_names_arr = explode(',', $context_fields_names_str);

        $context_arr = [];
        foreach ($context_fields_names_arr as $context_field_name){
            $context_arr[$context_field_name] = NullablePostFields::optionalFieldValue($context_field_name);
        }

        /** @var InterfaceWeight $obj */
        $obj = CRUDObjectLoader::createAndLoadObject($model_class_name, $model_id);
        $obj->swapWeights($context_arr);

        \OLOG\Redirects::redirectToSelf();
    }

    static protected function filterFormFieldName($table_id, $filter_index){
		return 'table_' . $table_id . '_filter_' . $filter_index;
	}

    static public function executeOperations(){
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
	static public function html($model_class_name, $create_form_html, $column_obj_arr, $filters_arr = [], $order_by = '', $table_id = '', $filters_position = self::FILTERS_POSITION_NONE)
	{

	    // TODO: придумать способ автогенерации table_id, который был бы уникальным, но при этом один и тот же когда одну таблицу запрашиваешь несколько раз
        self::executeOperations();

		//
		// вывод таблицы
		//

		$table_container_element_id = uniqid('tableContainer_');
		if ($table_id) {
			$table_container_element_id = $table_id;
		}

        // оборачиваем в отдельный div для выдачи только таблицы аяксом - иначе корневой элемент документа не будет доступен в jquery селекторах

		$html = HTML::div($table_container_element_id, '', function() use ($model_class_name, $create_form_html, $column_obj_arr, $filters_arr, $order_by, $table_id, $filters_position) {

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

			echo self::toolbarHtml($table_id, $create_form_html, $filters_arr);

			if ($filters_position == self::FILTERS_POSITION_TOP) {
				echo self::filtersHtml($table_id, $filters_arr);
			}

			if ($filters_position == self::FILTERS_POSITION_INLINE) {
				echo self::filtersHtmlInline($table_id, $filters_arr);
			}

			echo '<table class="table table-hover">';

			/** @var InterfaceCRUDTableColumn $column_obj */
			echo '<thead><tr>';
			foreach ($column_obj_arr as $column_obj) {
				Assert::assert($column_obj instanceof InterfaceCRUDTableColumn);
				echo '<th>' . Sanitize::sanitizeTagContent($column_obj->getTitle()) . '</th>';
			}
			echo '</tr></thead>';

			echo '<tbody>';
			$objs_ids_arr = CRUDInternalTableObjectsSelector::getObjIdsArrForClassName($table_id, $model_class_name, $filters_arr, $order_by);
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

			echo Pager::renderPager($table_id, count($objs_ids_arr));

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

            $filter_index = 0;

            foreach ($filters_arr as $filter_obj){
                /*
                if ($filter_obj instanceof InterfaceCRUDTableFilter) {

                    $html .= '<div class="col-md-12">';
                    $html .= '<div class="form-group">';

                    // TODO: finish
                    switch ($filter_obj->getOperationCode()) {
                        case (CRUDTableFilter::FILTER_LIKE):
                            $html .= '<label class="col-sm-4 text-right control-label">' . $filter_obj->getFieldName() . ' содержит:</label>';
                            $html .= '<div class="col-sm-8"><input class="form-control" name="' . self::filterFormFieldName($table_index_on_page, $filter_index) . '" value="' . $filter_obj->getValue() . '"></div>';
                            break;

                        case (CRUDTableFilter::FILTER_EQUAL):
                            $html .= '<label class="col-sm-4 text-right control-label">' . $filter_obj->getFieldName() . ' равно:</label>';
                            $html .= '<div class="col-sm-8">';

                            $input_name = self::filterFormFieldName($table_index_on_page, $filter_index);

                            if ($filter_obj->getWidgetObj()) {
                                $widget_obj = $filter_obj->getWidgetObj();
                                $html .= $widget_obj->htmlForValue($filter_obj->getValue(), $input_name);
                            } else {
                                $html .= '<input class="form-control" name="' . $input_name . '" value="' . $filter_obj->getValue() . '">';
                            }

                            $html .= '</div>';
                            break;

                        case (CRUDTableFilter::FILTER_IS_NULL):
                            $html .= '<label class="col-sm-4 text-right control-label">' . $filter_obj->getFieldName() . '</label>';
                            $html .= '<div class="col-sm-8">IS NULL</div>';
                            break;

                        case (CRUDTableFilter::FILTER_IN):
                            $html .= '<label class="col-sm-4 text-right control-label">' . $filter_obj->getFieldName() . '</label>';
                            $html .= '<div class="col-sm-8">IN ' . implode(', ', $filter_obj->getValue()) . '</div>';
                            break;

                        default:
                            throw new \Exception('filter type not supported');
                    }

                    $html .= '</div>';
                    $html .= '</div>';
                    $filter_index++;
                } else
                */

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
            //$html .= '<div class="row"><div class="col-sm-8 col-sm-offset-4"><button style="width: 100%;" type="submit" class="btn btn-default">Поиск</button></div></div>';
            $html .= '</form>';
            $html .= '</div>';
        }

        return $html;
    }

    static protected function filtersHtmlInline($table_index_on_page, $filters_arr)
    {
	    if (empty($filters_arr)) {
		    return '';
	    }

	    $html = HTML::div('filters-inline', '', function () use ($table_index_on_page, $filters_arr) {

		    echo '<form class="filters-form">';

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