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
    protected $is_required;

    public function __construct($field_name, $referenced_class_name, $referenced_class_title_field, $ajax_action_url, $editor_url, $is_required = false)
    {
        $this->setFieldName($field_name);
        $this->setAjaxActionUrl($ajax_action_url);
        $this->setReferencedClassName($referenced_class_name);
        $this->setReferencedClassTitleField($referenced_class_title_field);
        $this->setEditorUrl($editor_url);
        $this->setIsRequired($is_required);
    }

    public function html($obj)
    {
        $field_name = $this->getFieldName();
        $field_value = CRUDFieldsAccess::getObjectFieldValue($obj, $field_name);

        return $this->htmlForValue($field_value);
    }

    public function htmlForValue($field_value, $input_name = null)
    {
        $field_name = $this->getFieldName();

        if (is_null($input_name)){
            $input_name = $field_name;
        }

        $referenced_class_name = $this->getReferencedClassName();
        $referenced_class_title_field = $this->getReferencedClassTitleField();

        $referenced_obj_title = '';
        $disabled_btn_link = 'disabled';
        $is_null_value = '';

        if (is_null($field_value)){
            $is_null_value = "1";
        }

        if (!is_null($field_value)) {
            $referenced_obj = CRUDObjectLoader::createAndLoadObject($referenced_class_name, $field_value);
            $referenced_obj_title = CRUDFieldsAccess::getObjectFieldValue($referenced_obj, $referenced_class_title_field);
            $disabled_btn_link = '';
        }

        $is_required_str = '';
        if ($this->is_required){
            $is_required_str = ' required ';
        }

        $html = '';

        $select_element_id = 'js_select_' . rand(1, 999999);
		$choose_form_element_id = 'collapse_' . rand(1, 999999);

        $html .= '<input type="hidden" id="' . Sanitize::sanitizeAttrValue($select_element_id) . '" name="' . Sanitize::sanitizeAttrValue($input_name) . '" value="' . $field_value . '" data-field="' . Sanitize::sanitizeAttrValue($select_element_id) . '_text" ' . $is_required_str . '/>';
        $html .= '<input type="hidden" id="' . Sanitize::sanitizeAttrValue($select_element_id) . '_is_null" name="' . Sanitize::sanitizeAttrValue($input_name) . '___is_null" value="' . $is_null_value . '"/>';

        $html .= '<div class="input-group">';

        if ($this->getAjaxActionUrl()) {
            $html .= '<span class="input-group-btn">';
            $html .= '<button type="button" class="btn btn-default" data-toggle="modal" data-target="#' . $choose_form_element_id . '"><span class="glyphicon glyphicon-folder-open"></span></button>';
            $html .= '<button type="button" id="' . Sanitize::sanitizeAttrValue($select_element_id) . '_btn_is_null" class="btn btn-default"><span class="glyphicon glyphicon-remove"></span></button>';
            $html .= '</span>';
        }

        $html .= '<div class="form-control" id="' . Sanitize::sanitizeAttrValue($select_element_id) . '_text">' . $referenced_obj_title . '</div>';

        if ($this->getEditorUrl()) {
            $html .= '<span class="input-group-btn">';
            $html .= '<button ' . $disabled_btn_link . ' type="button" id="' . Sanitize::sanitizeAttrValue($select_element_id) . '_btn_link" class="btn btn-default">Перейти</button>';
            $html .= '</span>';
        }

        $html .= '</div>';

        $html .= BT::modal($choose_form_element_id, 'Выбрать');

        ob_start();?>

        <script>
            $('#<?= $choose_form_element_id ?>').on('hidden.bs.modal', function () {
	            $('#<?= $choose_form_element_id ?> .modal-body').html('');
            });

            $('#<?= $choose_form_element_id ?>').on('shown.bs.modal', function (e) {
                $.ajax({
                    url: "<?= $this->getAjaxActionUrl() ?>"
                }).success(function(received_html) {
                    $('#<?= $choose_form_element_id ?> .modal-body').html(received_html);
                });
            });

            $('#<?= $choose_form_element_id ?>').on('click', '.js-ajax-form-select', function (e) {
            	e.preventDefault();
                var select_id = $(this).data('id');
                var select_title = $(this).data('title');
				$('#<?= $choose_form_element_id ?>').modal('hide');
				$('#<?= $select_element_id ?>_text').text(select_title);
                $('#<?= $select_element_id ?>_btn_link').attr('disabled', false);
				$('#<?= $select_element_id ?>').val(select_id).trigger('change');
				$('#<?= $select_element_id ?>_is_null').val('');
			});

			$('#<?= $select_element_id ?>_btn_is_null').on('click', function (e) {
				e.preventDefault();
				$('#<?= $select_element_id ?>_text').text('');
                $('#<?= $select_element_id ?>_btn_link').attr('disabled', true);
				$('#<?= $select_element_id ?>').val('').trigger('change');
				$('#<?= $select_element_id ?>_is_null').val(1);
			});

            $('#<?= $select_element_id ?>_btn_link').on('click', function (e) {
                var url = '<?= $this->getEditorUrl() ?>';
                var id = $('#<?= $select_element_id ?>').val();
                url = url.replace('REFERENCED_ID', id);

                window.location = url;
            });
        </script>

        <?php
        $html .= ob_get_clean();

        return $html;
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