<?php

namespace OLOG\CRUD;

use OLOG\BT\BT;
use OLOG\Sanitize;

class CRUDFormWidgetReferenceAjax implements InterfaceCRUDFormWidget
{
    protected $field_name;
    protected $ajax_action_url;
    protected $referenced_class_name;
    protected $referenced_class_title_field;
    protected $editor_url;

    public function __construct($field_name, $referenced_class_name, $referenced_class_title_field, $ajax_action_url, $editor_url)
    {
        $this->setFieldName($field_name);
        $this->setAjaxActionUrl($ajax_action_url);
        $this->setReferencedClassName($referenced_class_name);
        $this->setReferencedClassTitleField($referenced_class_title_field);
        $this->setEditorUrl($editor_url);
    }

    public function html($obj)
    {
        $field_name = $this->getFieldName();
        $referenced_class_name = $this->getReferencedClassName();
        $referenced_class_title_field = $this->getReferencedClassTitleField();

        $field_value = CRUDFieldsAccess::getObjectFieldValue($obj, $field_name);

        $referenced_obj_title = '';

        if (!is_null($field_value)) {
            $referenced_obj = CRUDObjectLoader::createAndLoadObject($referenced_class_name, $field_value);
            $referenced_obj_title = CRUDFieldsAccess::getObjectFieldValue($referenced_obj, $referenced_class_title_field);
        }


        $html = '';

        $select_element_id = 'js_select_' . rand(1, 999999);
		$choose_form_element_id = 'collapse_' . rand(1, 999999);

        $html .= '<div class="input-group">';
		$html .= '<span class="input-group-btn">';
		$html .= '<button type="button" class="btn btn-default" data-toggle="modal" data-target="#' . $choose_form_element_id . '">Выбрать</button>';
		$html .= '</span>';
        $html .= '<div id="' . Sanitize::sanitizeAttrValue($select_element_id) . '_text" class="form-control">' . $referenced_obj_title . '</div>';
		$html .= '<input type="hidden" id="' . Sanitize::sanitizeAttrValue($select_element_id) . '" name="' . Sanitize::sanitizeAttrValue($field_name) . '"/>';
        $html .= '<input type="hidden" id="' . Sanitize::sanitizeAttrValue($select_element_id) . '_is_null" name="' . Sanitize::sanitizeAttrValue($field_name) . '___is_null"/>';
        $html .= '<span class="input-group-btn">';
		$html .= '<button type="button" id="' . Sanitize::sanitizeAttrValue($select_element_id) . '_btn_is_null" class="btn btn-default" data-toggle="modal">X</button>';
        $html .= '</span>';
        $html .= '</div>';

        $html .= BT::modal($choose_form_element_id, 'Выбрать');

        ob_start();?>

        <script>
            $('#<?= $choose_form_element_id ?>').on('shown.bs.modal', function (e) {
                $.ajax({
                    url: "<?= $this->getAjaxActionUrl() ?>"
                }).success(function(received_html) {
                    $('#<?= $choose_form_element_id ?> .modal-body').html(received_html);
                });
            }).on('click', '.js-ajax-form-select', function (e) {
            	e.preventDefault();
                var select_id = $(this).data('id');
                var select_title = $(this).data('title');
				$('#<?= $choose_form_element_id ?>').modal('hide');
				$('#<?= $select_element_id ?>_text').text(select_title);
				$('#<?= $select_element_id ?>').val(select_id);
				$('#<?= $select_element_id ?>_is_null').val('');
			});
			$('#<?= $select_element_id ?>_btn_is_null').on('click', function (e) {
				e.preventDefault();
				$('#<?= $select_element_id ?>_text').text('');
				$('#<?= $select_element_id ?>').val('');
				$('#<?= $select_element_id ?>_is_null').val(1);
			});
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
    public function getAjaxActionUrl()
    {
        return $this->ajax_action_url;
    }

    /**
     * @param mixed $ajax_action_url
     */
    public function setAjaxActionUrl($ajax_action_url)
    {
        $this->ajax_action_url = $ajax_action_url;
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

    /**
     * @return mixed
     */
    public function getEditorUrl()
    {
        return $this->editor_url;
    }

    /**
     * @param mixed $editor_url
     */
    public function setEditorUrl($editor_url)
    {
        $this->editor_url = $editor_url;
    }


}