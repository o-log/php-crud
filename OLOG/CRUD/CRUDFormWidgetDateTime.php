<?php

namespace OLOG\CRUD;

use OLOG\Sanitize;

class CRUDFormWidgetDateTime implements InterfaceCRUDFormWidget
{
    protected $field_name;

    public function __construct($field_name)
    {
        $this->setFieldName($field_name);
    }

    public function html($obj)
    {
	    static $CRUDFormWidgetDateTime_include_script;
	    
        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($obj, $field_name);
        
        /* Нужно изменить на нах CDN */
        $script = '';
        $uniqid = uniqid('CRUDFormWidgetDateTime_');
        if(!isset($CRUDFormWidgetDateTime_include_script)){
			$script = '
								<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.12.0/moment.min.js"></script>
								<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.12.0/locale/ru.js"></script>
				<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
								<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
			';
	        $CRUDFormWidgetDateTime_include_script = false;
        }
        
        return $script . '
        	<input type="hidden" id="' . $uniqid . '_input" name="' . Sanitize::sanitizeAttrValue($field_name) . '" value="' . Sanitize::sanitizeTagContent($field_value) . '">
        	<div class="input-group date" id="' . $uniqid . '">
        		<input type="text" class="form-control" value="' . date('d-m-Y H:i:s', strtotime($field_value)) . '">
    			<span class="input-group-addon">
    				<span class="glyphicon glyphicon-calendar"></span>
    			</span>
        	</div>
        	<script>
        	$("#' . $uniqid . '").datetimepicker({
			    format: "DD-MM-YYYY HH:mm:ss",
                sideBySide: true,
                showTodayButton: true
            }).on("dp.change", function(obj){
	            $("#' . $uniqid . '_input").val(obj.date.format("YYYY-MM-DD HH:mm:ss"));
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