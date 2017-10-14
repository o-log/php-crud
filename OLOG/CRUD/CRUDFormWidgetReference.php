<?php

namespace OLOG\CRUD;

use OLOG\HTML;

class CRUDFormWidgetReference implements InterfaceCRUDFormWidget
{
    protected $field_name;
    protected $referenced_class_name;
    protected $referenced_class_title_field;

    public function __construct($field_name, $referenced_class_name, $referenced_class_title_field)
    {
        $this->setFieldName($field_name);
        $this->setReferencedClassName($referenced_class_name);
        $this->setReferencedClassTitleField($referenced_class_title_field);
    }

    public function html($obj)
    {
        $field_name = $this->getFieldName();
        $referenced_class_name = $this->getReferencedClassName();
        $referenced_class_title_field = $this->getReferencedClassTitleField();

        $field_value = CRUDFieldsAccess::getObjectFieldValue($obj, $field_name);

        $options_html_arr = ['<option value=""></option>'];

        // TODO: check referenced class interfaces

        $referenced_obj_ids_arr = \OLOG\DB\DB::readColumn(
            $referenced_class_name::DB_ID, // TODO: use common method
            'select ID from ' . $referenced_class_name::DB_TABLE_NAME . ' order by ID' // TODO: respect custom ID fields
        );

        $options_arr = [];
        foreach ($referenced_obj_ids_arr as $id){
            $obj = CRUDObjectLoader::createAndLoadObject($referenced_class_name, $id);
            $options_arr[$id] = CRUDFieldsAccess::getObjectFieldValue($obj, $referenced_class_title_field);
        }

        foreach($options_arr as $value => $title)
        {
            $selected_html_attr = '';
            if ($field_value == $value) {
                $selected_html_attr = ' selected';
            }

            $options_html_arr[] = '<option value="' .  $value . '"' . $selected_html_attr . '>' . $title . '</option>'; // TODO: sanitize
        }

        $html = '';

        $select_element_id = 'js_select_' . rand(1, 999999);

        $html .= '<select id="' . HTML::attr($select_element_id) . '" name="' . HTML::attr($field_name) . '" class="form-control">' . implode('', $options_html_arr) . '</select>';
        $html .= '<input type="hidden" id="' . HTML::attr($select_element_id) . '_is_null" name="' . HTML::attr($field_name) . '___is_null"/>';

        ob_start();?>
        <script>
            var select_element = document.getElementById('<?= $select_element_id ?>');
            select_element.addEventListener(
                'change',
                function(){
                    var select_element_id = document.getElementById('<?= $select_element_id ?>');
                    var is_null_element = document.getElementById('<?= $select_element_id ?>_is_null');
                    var value = select_element_id.options[select_element_id.selectedIndex].value;

                    if (value == ''){
                        is_null_element.value = '1';
                    } else {
                        is_null_element.value = '';
                    }
                }
            );

            select_element.dispatchEvent(new Event('change')); // fire to initialize is_null input on display
        </script>

        <?php
        $html .= ob_get_clean();

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
    public function getReferencedClassName()
    {
        return $this->referenced_class_name;
    }

    /**
     * @param mixed $referenced_class_name
     */
    public function setReferencedClassName($referenced_class_name)
    {
        $this->referenced_class_name = $referenced_class_name;
    }

    /**
     * @return mixed
     */
    public function getReferencedClassTitleField()
    {
        return $this->referenced_class_title_field;
    }

    /**
     * @param mixed $referenced_class_title_field
     */
    public function setReferencedClassTitleField($referenced_class_title_field)
    {
        $this->referenced_class_title_field = $referenced_class_title_field;
    }


}