<?php

namespace OLOG\CRUD;

class CRUDWidgetReference
{
    public static function html($field_name, $field_value, $referenced_class_name, $referenced_class_title_field)
    {
        $options_html_arr = ['<option value=""></option>'];

        // TODO: check referenced class interfaces

        $referenced_obj_ids_arr = \OLOG\DB\DBWrapper::readColumn(
            $referenced_class_name::DB_ID, // TODO: use common method
            'select ID from ' . $referenced_class_name::DB_TABLE_NAME . ' order by ID' // TODO: respect custom ID fields
        );

        $options_arr = [];
        foreach ($referenced_obj_ids_arr as $id){
            $obj = ObjectLoader::createAndLoadObject($referenced_class_name, $id);
            $options_arr[$id] = FieldsAccess::getObjectFieldValue($obj, $referenced_class_title_field);
        }

        // TODO: send to common options widget?

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

        // TODO: sanitize field name
        $html .= '<select id="' . $select_element_id . '" name="' . $field_name . '" class="form-control">' . implode('', $options_html_arr) . '</select>';

        // TODO: sanitize field name
        $html .= '<input type="hidden" id="' . $select_element_id . '_is_null" name="' . $field_name . '___is_null"/>';

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
}