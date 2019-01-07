<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

use OLOG\HTML;

class FWRadios implements FWInterface
{
    protected $field_name;
    protected $options_arr;
    protected $show_null_checkbox;
    protected $is_required;
    protected $disabled;

    public function __construct($field_name, $options_arr, $show_null_checkbox = false, $is_required = false, $disabled = false)
    {
        $this->setFieldName($field_name);
        $this->setOptionsArr($options_arr);
        $this->setShowNullCheckbox($show_null_checkbox);
        $this->setIsRequired($is_required);
        $this->setDisabled($disabled);
    }

    public function html($obj)
    {
        $field_name = $this->getFieldName();
        $field_value = CInternalFieldsAccess::getObjectFieldValue($obj, $field_name);

        return $this->htmlForValue($field_value);
    }

    public function htmlForValue($field_value, $input_name = null)
    {
        $field_name = $this->getFieldName();

        if (is_null($input_name)){
            $input_name = $field_name;
        }

        $uniqid = uniqid('CRUDFormWidgetRadios_');
        $input_cols = $this->getShowNullCheckbox() ? '10' : '12';

        $html = '';
        //$html .= '<div class="row">';
        //$html .= '<div class="col-sm-' . $input_cols . '" id="' . $uniqid . '_radio_box">';
        $html .= '<div class="btn-group btn-group-sm" data-toggle="buttons" id="' . $uniqid . '_radio_box">';

        $options_arr = $this->getOptionsArr();

        $disabled = '';
        if ($this->getDisabled()) {
            $disabled = 'disabled';
        }

        foreach ($options_arr as $value => $title) {
            $selected_html_attr = '';
            $label_class = '';
            if (!is_null($field_value) && $field_value == $value) {
                $selected_html_attr = ' checked ';
                $label_class .= ' active ';
            }

            $is_required_str = '';
            if ($this->is_required) {
                $is_required_str = ' required ';
            }

            $html .= '<label class="btn btn-secondary ' . $label_class . '"><input type="radio" name="' . HTML::attr($input_name) . '" value="' . HTML::attr($value) . '" ' . $selected_html_attr . ' ' . $is_required_str . ' ' .$disabled. '> ' . $title . '</label>';
        }
        //$html .= '</div>';

        if ($this->getShowNullCheckbox()) {

            $is_null_checked = '';
            if (is_null($field_value)) {
                $is_null_checked = ' checked ';
            }
            ob_start(); ?>
                <label>
                    <input id="<?= $uniqid ?>___is_null" type="checkbox" value="1" name="<?= HTML::attr($input_name) ?>___is_null" <?= $is_null_checked ?>> NULL
                </label>
            <script>
                (function () {
                    var $input_is_null = $('#<?= $uniqid ?>___is_null');
                    var $input = $('#<?= $uniqid ?>_radio_box').find('input[type="radio"]');

                    $input.on('change', function () {
                        $input_is_null.prop('checked', false);
                    });

                    $input_is_null.on('change', function () {
                        if ($(this).is(':checked')) {
                            $input.prop('checked', false);
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
    public function getOptionsArr()
    {
        return $this->options_arr;
    }

    /**
     * @param mixed $options_arr
     */
    public function setOptionsArr($options_arr)
    {
        $this->options_arr = $options_arr;
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
     * @return bool
     */
    public function getDisabled()
    {
        return $this->disabled;
    }

    /**
     * @param bool $disabled
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
    }
}
