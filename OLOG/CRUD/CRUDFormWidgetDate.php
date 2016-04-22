<?php

namespace OLOG\CRUD;

use OLOG\Sanitize;

class CRUDFormWidgetDate implements InterfaceCRUDFormWidget
{
    protected $field_name;

    public function __construct($field_name)
    {
        $this->setFieldName($field_name);
    }

    public function html($obj)
    {
	    static $CRUDFormWidgetDate_include_script;
	    
        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($obj, $field_name);
        
        /* Нужно изменить на нах CDN */
        $script = '';
        $uniqid = uniqid('CRUDFormWidgetDate_');
        if(!isset($CRUDFormWidgetDate_include_script)){
			$script = '
								<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.12.0/moment.min.js"></script>
								<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.12.0/locale/ru.js"></script>
				<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
								<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
			';
	        $CRUDFormWidgetDate_include_script = false;
        }

        $is_null_checked = '';
        if (is_null($field_value)){
            $is_null_checked = ' checked ';
        }

        $field_value_attr = '';
        if ($field_value) {
            $field_value_attr = date('d-m-Y', strtotime($field_value));
        }

        return $script . '
            <input type="hidden" id="' . $uniqid . '_input" name="' . Sanitize::sanitizeAttrValue($field_name) . '" value="' . Sanitize::sanitizeTagContent($field_value) . '">
            <div class="row">
                <div class="col-sm-10">
                    <div class="input-group date" id="' . $uniqid . '">
                        <input type="text" class="form-control" value="' . $field_value_attr . '">
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label class="form-control-static">
                        <input type="checkbox" value="1" name="' . Sanitize::sanitizeAttrValue($field_name) . '___is_null" ' . $is_null_checked . ' /> NULL
                    </label>
                </div>
            </div>

            <script>
            $("#' . $uniqid . '").datetimepicker({
                format: "DD-MM-YYYY",
                sideBySide: true,
                showTodayButton: true
            }).on("dp.change", function(obj){
                $("#' . $uniqid . '_input").val(obj.date.format("YYYY-MM-DD"));
            });
            </script>
        ';
        
    }

    /**
     * @return mixed
     */
    public function getFieldName()
    {
        return $this->field_name;
    }

    /**
     * @param mixed $field_name
     */
    public function setFieldName($field_name)
    {
        $this->field_name = $field_name;
    }


}

