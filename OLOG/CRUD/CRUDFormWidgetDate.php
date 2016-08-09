<?php

namespace OLOG\CRUD;

use OLOG\Sanitize;

class CRUDFormWidgetDate implements InterfaceCRUDFormWidget
{
    protected $field_name;
    protected $show_null_checkbox;
    protected $is_required;

    public function __construct($field_name, $show_null_checkbox = false, $is_required = false)
    {
        $this->setFieldName($field_name);
        $this->setShowNullCheckbox($show_null_checkbox);
        $this->setIsRequired($is_required);
    }

    public function html($obj)
    {
        static $CRUDFormWidgetDate_include_script;

        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($obj, $field_name);

        /* Нужно изменить на нах CDN */
        $script = '';
        $uniqid = uniqid('CRUDFormWidgetDate_');
        if (!isset($CRUDFormWidgetDate_include_script)) {
            $script = '
                <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.12.0/moment.min.js"></script>
                <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.12.0/locale/ru.js"></script>
                <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
                <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
			';
            $CRUDFormWidgetDate_include_script = false;
        }

        $is_null_checked = '';
        if (is_null($field_value)) {
            $is_null_checked = ' checked ';
        }

        $field_value_attr = '';
        if ($field_value) {
            $field_value_attr = date('d-m-Y', strtotime($field_value));
        }

        $is_required_str = '';
        if ($this->is_required) {
            $is_required_str = ' required ';
        }

        $input_cols = $this->getShowNullCheckbox() ? '10' : '12';

        $html = '';
        $html .= '
            <input type="hidden" id="' . $uniqid . '_input" name="' . Sanitize::sanitizeAttrValue($field_name) . '" value="' . Sanitize::sanitizeTagContent($field_value) . '" data-field="' . $uniqid . '_date" ' . $is_required_str . '>
            <div class="row">
                <div class="col-sm-' . $input_cols . '">
                    <div class="input-group date" id="' . $uniqid . '">
                        <input id="' . $uniqid . '_date" type="text" class="form-control" value="' . $field_value_attr . '">
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
        ';

        if ($this->getShowNullCheckbox()) {
            $html .= '
                <div class="col-sm-2">
                    <label class="form-control-static">
                        <input type = "checkbox" value = "1" name = "' . Sanitize::sanitizeAttrValue($field_name) . '___is_null" ' . $is_null_checked . ' /> null
                    </label >
                </div>
            ';
        }
        $html .= '</div>';

        ob_start(); ?>
        <script>
            $("#<?= $uniqid ?>").datetimepicker({
                format: "DD-MM-YYYY",
                sideBySide: true,
                showTodayButton: true
            }).on(
                "dp.change", function (obj) {
                    $("#<?= $uniqid ?>_input").val(obj.date.format("YYYY-MM-DD")).trigger('change');
                }
            );
        </script>
        <?php
        $html .= ob_get_clean();

        return $script . $html;
    }

    /**
     * @return mixed
     */
    public function getIsRequired()
    {
        return $this->is_required;
    }

    /**
     * @param mixed $is_required
     */
    public function setIsRequired($is_required)
    {
        $this->is_required = $is_required;
    }

    /**
     * @return mixed
     */
    public function getShowNullCheckbox()
    {
        return $this->show_null_checkbox;
    }

    /**
     * @param mixed $show_null_checkbox
     */
    public function setShowNullCheckbox($show_null_checkbox)
    {
        $this->show_null_checkbox = $show_null_checkbox;
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

