<?php

namespace OLOG\CRUD;

use OLOG\HTML;

class CRUDFormWidgetInput implements InterfaceCRUDFormWidget
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
        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($obj, $field_name);

        $is_required_str = '';
        if ($this->is_required) {
            $is_required_str = ' required ';
        }

        $uniqid = uniqid('CRUDFormWidgetInput_');
        $input_cols = $this->getShowNullCheckbox() ? '10' : '12';

        $html = '';
        $html .= '<div class="row">';
        $html .= '<div class="col-sm-' . $input_cols . '">';
        $html .= '<input id="' . $uniqid . '_input" name="' . HTML::attr($field_name) . '" ' . $is_required_str . ' class="form-control" value="' . HTML::attr($field_value) . '"/>';
        $html .= '</div>';

        if ($this->getShowNullCheckbox()) {

            $is_null_checked = '';
            if (is_null($field_value)) {
                $is_null_checked = ' checked ';
            }
            ob_start(); ?>
            <div class="col-sm-2">
                <label class="form-control-static">
                    <input id="<?= $uniqid ?>___is_null" type="checkbox" value="1" name="<?= HTML::attr($field_name) ?>___is_null" <?= $is_null_checked ?>> NULL
                </label>
            </div>
            <script>
                (function () {
                    var $input_is_null = $('#<?= $uniqid ?>___is_null');
                    var $input = $('#<?= $uniqid ?>_input');

                    $input.on('change keydown', function () {
                        $input_is_null.prop('checked', false);
                    });

                    $input_is_null.on('change', function () {
                        if ($(this).is(':checked')) {
                            $input.val('');
                        }
                    });
                })();
            </script>
            <?php
            $html .= ob_get_clean();
        }

        $html .= '</div>';

        return $html;
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

}