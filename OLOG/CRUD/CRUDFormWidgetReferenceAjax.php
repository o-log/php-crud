<?php

namespace OLOG\CRUD;

use OLOG\Sanitize;

class CRUDFormWidgetReferenceAjax implements InterfaceCRUDFormWidget
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


        $html = '';

        $select_element_id = 'js_select_' . rand(1, 999999);
		$create_form_element_id = 'collapse_' . rand(1, 999999);

        $html .= '<div class="input-group">';
		$html .= '<span class="input-group-btn">';
		$html .= '<button type="button" class="btn btn-default" data-toggle="modal" data-target="#' . $create_form_element_id . '">Создать</button>';
		$html .= '</span>';
        $html .= '<div id="' . Sanitize::sanitizeAttrValue($select_element_id) . '_text" class="form-control">' . $field_value . '</div>';
		$html .= '<input type="hidden" id="' . Sanitize::sanitizeAttrValue($select_element_id) . '" name="' . Sanitize::sanitizeAttrValue($field_name) . '"/>';
        $html .= '<input type="hidden" id="' . Sanitize::sanitizeAttrValue($select_element_id) . '_is_null" name="' . Sanitize::sanitizeAttrValue($field_name) . '___is_null"/>';
        $html .= '<span class="input-group-btn">';
		$html .= '<button type="button" id="' . Sanitize::sanitizeAttrValue($select_element_id) . '_btn_is_null" class="btn btn-default" data-toggle="modal">X</button>';
        $html .= '</span>';
        $html .= '</div>';

		$html .= '<div class="modal fade" id="' . $create_form_element_id . '" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="gridSystemModalLabel">Форма создания</h4></div><div class="modal-body"></div></div></div></div><!-- /.modal -->';

        ob_start();?>

        <script>
            $('#<?= $create_form_element_id ?>').on('shown.bs.modal', function (e) {
                $.ajax({
                    url: "/ajax_terms"
                }).success(function(received_html) {
                    $('#<?= $create_form_element_id ?> .modal-body').html(received_html);
                });
            }).on('click', '.js-ajax-form-select', function (e) {
            	e.preventDefault();
				var select_id = $(this).data('id');
				$('#<?= $create_form_element_id ?>').modal('hide');
				$('#<?= $select_element_id ?>_text').text(select_id);
				$('#<?= $select_element_id ?>').var(select_id);
				$('#<?= $select_element_id ?>_is_null').val('');
			});
			$('#<?= $select_element_id ?>_btn_is_null').on('click', function (e) {
				e.preventDefault();
				$('#<?= $select_element_id ?>_text').text('');
				$('#<?= $select_element_id ?>').var('');
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