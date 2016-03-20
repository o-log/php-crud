<?php

namespace OLOG\CRUD;

class CRUDWidgetAceTextarea
{
    public static function generateHtml($field_name, $field_value)
    {
        $editor_element_id = 'editor_' . time() . '_' . rand(1, 999999);
        $html = '';

        $html .= '
            <style>
             #' . $editor_element_id . ' {
                position: relative;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        height: 500px;
            }
            </style>
            ';

        // TODO: is form-control needed?
        $html .= '<div id="' . $editor_element_id . '" class="form-control">' . Sanitize::sanitizeTagContent($field_value) . '</div>';
        $html .= '<textarea id="' . $editor_element_id . '_target" name="' . Sanitize::sanitizeAttrValue($field_name) . '" style="display: none;">' . Sanitize::sanitizeTagContent($field_value) . '</textarea>';

        // TODO: multiple insertion!!!!
        $html .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.3/ace.js" type="text/javascript" charset="utf-8"></script>
            <script>
            //var editor_element = document.getElementById("' . $editor_element_id . '");
            //editor_element.parentElement.style.height = "500px";
            var editor = ace.edit("' . $editor_element_id . '");

            // TODO: enable another modes
            editor.getSession().setMode("ace/mode/html");

            editor.getSession().on("change", function() {
                var target = document.getElementById("' . $editor_element_id . '_target");
                //var editor_element = document.getElementById("' . $editor_element_id . '");
                target.innerHTML = editor.getSession().getValue();
            });
            </script>
            ';

        return $html;
    }


}