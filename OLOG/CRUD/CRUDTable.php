<?php

namespace OLOG\CRUD;

use OLOG\Assert;
use OLOG\BT\BT;
use OLOG\GETAccess;
use OLOG\Model\InterfaceWeight;
use OLOG\Operations;
use OLOG\POSTAccess;
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


    static public function readFiltersValuesFromRequest($table_id, $filters_arr)
    {
        $filter_index = 0;

        /** @var InterfaceCRUDTableFilter $filter_obj */
        foreach ($filters_arr as $filter_obj) {
            if ($filter_obj instanceof InterfaceCRUDTableFilter) {

                $filter_field_name = self::filterFormFieldName($table_id, $filter_index);

                if (array_key_exists($filter_field_name, $_GET)) {
                    $filter_obj->setValue(urldecode($_GET[$filter_field_name]));
                }

                $filter_index++;
            } elseif ($filter_obj instanceof InterfaceCRUDTableFilter2) {
                // DO NOTHING - FILTER WILL READ ITS VALUE WHEN REQUIRED
            } else {
                throw new \Exception('filter doesnt implement InterfaceCRUDTableFilter nor InterfaceCRUDTableFilter2');
            }
        }

        return $filters_arr;
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
	static public function html($model_class_name, $create_form_html, $column_obj_arr, $filters_arr = [], $order_by = '', $table_id = '1', $filters_position = self::FILTERS_POSITION_NONE)
	{

	    // TODO: придумать способ автогенерации table_id, который был бы уникальным, но при этом один и тот же когда одну таблицу запрашиваешь несколько раз

		static $CRUDTable_include_script;

        self::executeOperations();

        $script = '';

		// include script only once per page
		if(!isset($CRUDTable_include_script)){
		    $script = '';
			$script .= '<script src="//cdnjs.cloudflare.com/ajax/libs/js-url/2.3.0/url.min.js"></script>';
            $script .= '<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>';
            $script .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css">';

			$script .= '<script>';
            $script .= Render::callLocaltemplate('templates/crudtable.js');
			$script .= '</script>';

			$CRUDTable_include_script = false;
		}

        $filters_arr = self::readFiltersValuesFromRequest($table_id, $filters_arr);
        $objs_ids_arr = CRUDInternalTableObjectsSelector::getObjIdsArrForClassName($table_id, $model_class_name, $filters_arr, $order_by);

		//
		// вывод таблицы
		//

		$table_container_element_id = 'tableContainer_' . $table_id;

        // оборачиваем в отдельный div для выдачи только таблицы аяксом - иначе корневой элемент документа не будет доступен в jquery селекторах
		$html = '<div>'; // container div
        $html .= '<div class="' . $table_container_element_id . ' row">';

        if ($filters_position == self::FILTERS_POSITION_LEFT) {
            $html .= '<div class="col-sm-4">';
            $html .= self::filtersHtml($table_id, $filters_arr);
            $html .= '</div>';
        }

        if (($filters_position == self::FILTERS_POSITION_LEFT) || ($filters_position == self::FILTERS_POSITION_RIGHT)) {
            $html .= '<div class="col-sm-8">';
        } else {
            $html .= '<div class="col-sm-12">';
        }

		$html .= self::toolbarHtml($table_id, $create_form_html, $filters_arr);

        if ($filters_position == self::FILTERS_POSITION_TOP) {
            $html .= self::filtersHtml($table_id, $filters_arr);
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

		foreach ($objs_ids_arr as $obj_id) {
			$obj_obj = CRUDObjectLoader::createAndLoadObject($model_class_name, $obj_id);

			$html .= '<tr>';

			/** @var InterfaceCRUDTableColumn $column_obj */
			foreach ($column_obj_arr as $column_obj) {
				Assert::assert($column_obj instanceof InterfaceCRUDTableColumn);

				$html .= '<td>';

				/** @var InterfaceCRUDTableWidget $widget_obj */
				$widget_obj = $column_obj->getWidgetObj();

				Assert::assert($widget_obj);
				Assert::assert($widget_obj instanceof InterfaceCRUDTableWidget);

				$html .= $widget_obj->html($obj_obj);

				$html .= '</td>';

			}

			$html .= '</tr>';
		}

		$html .= '</tbody>';
		$html .= '</table>';

		$html .= Pager::renderPager($table_id, count($objs_ids_arr));

        $html .= '</div>';

        if ($filters_position == self::FILTERS_POSITION_RIGHT) {
            $html .= '<div class="col-sm-4">';
            $html .= self::filtersHtml($table_id, $filters_arr);
            $html .= '</div>';
        }

        $html .= '</div>'; // row div

        $html .= '</div>'; // container div

        $html .= '<script>CRUD.Table.init("' . $table_container_element_id . '", "' . Url::getCurrentUrlNoGetForm() . '");</script>';

		return $script . $html;
	}

    static protected function filtersHtml($table_index_on_page, $filters_arr)
    {
        $html = '';


        if ($filters_arr) {
            $html .= '<div class="">';
            $html .= '<form class="filters-form form-horizontal">';
            $html .= '<div class="row">';

            $filter_index = 0;

            /** @var InterfaceCRUDTableFilter $filter_obj */
            foreach ($filters_arr as $filter_obj){
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
                                /** @var InterfaceCRUDFormWidget $widget_obj */
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
                } elseif ($filter_obj instanceof InterfaceCRUDTableFilter2) {
                    $html .= '<div class="col-md-12">';
                    $html .= '<div class="form-group">';

                    $html .= '<label class="col-sm-4 text-right control-label">' . $filter_obj->getTitle() . '</label>';
                    $html .= '<div class="col-sm-8">' . $filter_obj->getHtml() . '</div>';

                    $html .= '</div>';
                    $html .= '</div>';
                } elseif ($filter_obj instanceof InterfaceCRUDTableFilter2) {
                    // do nothing with invisible filters
                } else {
                    throw new \Exception('filter doesnt implement interface ...');
                }
            }

            $html .= '</div>';
            $html .= '<div class="row"><div class="col-sm-8 col-sm-offset-4"><button style="width: 100%;" type="submit" class="btn btn-default">Поиск</button></div></div>';
            $html .= '</form>';
            $html .= '</div>';
        }

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