<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\CRUD;

use OLOG\HTML;

class FWTextarea implements FWInterface
{
    protected $field_name;
    protected $is_required;
    protected $disabled;


    public function __construct($field_name, $is_required = false, $disabled = false)
    {
        $this->setFieldName($field_name);
        $this->setIsRequired($is_required);
        $this->setDisabled($disabled);
    }

    public function html($obj)
    {
        $field_name = $this->getFieldName();
        $field_value = CInternalFieldsAccess::getObjectFieldValue($obj, $field_name);
        $is_required_str = '';
        if ($this->is_required) {
            $is_required_str = ' required ';
        }

        $disabled = '';
        if ($this->getDisabled()) {
            $disabled = 'disabled';
        }

        return '<textarea name="' . HTML::attr($field_name) . '"  '. $is_required_str . ' class="form-control" rows="5"  ' . $disabled . '>' . HTML::content($field_value) . '</textarea>';
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
