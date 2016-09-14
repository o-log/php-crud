<?php

namespace OLOG\CRUD;

use OLOG\Sanitize;

class CRUDFormWidgetMediumEditor implements InterfaceCRUDFormWidget
{
	protected $field_name;
    protected $uniqid;

	public function __construct($field_name, $uniqid = '')
	{
		$this->setFieldName($field_name);
        if ($uniqid) {
            $this->setUniqid($uniqid);
        } else {
            $this->setUniqid(uniqid('CRUDFormWidgetMediumEditor_'));
        }
	}

	public function html($obj)
	{
		static $CRUDFormWidgetMediumEditor_include_script;

		$field_name = $this->getFieldName();
		$field_value = CRUDFieldsAccess::getObjectFieldValue($obj, $field_name);

		/* Нужно изменить на нах CDN */
		$script = '';
		$uniqid = $this->getUniqid();
		if (!isset($CRUDFormWidgetMediumEditor_include_script)) {
			$script = '
				<script src="//cdn.jsdelivr.net/medium-editor/latest/js/medium-editor.min.js"></script>
				<link rel="stylesheet" href="//cdn.jsdelivr.net/medium-editor/latest/css/medium-editor.min.css" type="text/css" media="screen" charset="utf-8">
				<style>.medium-toolbar-arrow-under:after{border-color:#428bca transparent transparent;top:60px}.medium-toolbar-arrow-over:before{border-color:transparent transparent #428bca}.medium-editor-toolbar{background-color:#428bca;border:1px solid #357ebd;border-radius:4px}.medium-editor-toolbar li button{background-color:transparent;border:none;border-right:1px solid #357ebd;box-sizing:border-box;color:#fff;height:60px;min-width:60px;-webkit-transition:background-color .2s ease-in,color .2s ease-in;transition:background-color .2s ease-in,color .2s ease-in}.medium-editor-toolbar li .medium-editor-button-active,.medium-editor-toolbar li button:hover{background-color:#3276b1;color:#fff}.medium-editor-toolbar li .medium-editor-button-first{border-bottom-left-radius:4px;border-top-left-radius:4px}.medium-editor-toolbar li .medium-editor-button-last{border-bottom-right-radius:4px;border-right:none;border-top-right-radius:4px}.medium-editor-toolbar-form{background:#428bca;border-radius:4px;color:#fff}.medium-editor-toolbar-form .medium-editor-toolbar-input{background:#428bca;color:#fff;height:60px}.medium-editor-toolbar-form .medium-editor-toolbar-input::-webkit-input-placeholder{color:#fff;color:rgba(255,255,255,.8)}.medium-editor-toolbar-form .medium-editor-toolbar-input:-moz-placeholder{color:#fff;color:rgba(255,255,255,.8)}.medium-editor-toolbar-form .medium-editor-toolbar-input::-moz-placeholder{color:#fff;color:rgba(255,255,255,.8)}.medium-editor-toolbar-form .medium-editor-toolbar-input:-ms-input-placeholder{color:#fff;color:rgba(255,255,255,.8)}.medium-editor-toolbar-form a{color:#fff}.medium-editor-toolbar-anchor-preview{background:#428bca;border-radius:4px;color:#fff}.medium-editor-placeholder:after{color:#357ebd}</style>
			';
			$CRUDFormWidgetMediumEditor_include_script = false;
		}

		$html = '';

		$html .= '<textarea id="' . $uniqid . '_textarea" name="' . Sanitize::sanitizeAttrValue($field_name) . '" style="display: none;">' . $field_value . '</textarea>';
		$html .= '<div id="' . $uniqid . '" class="form-control" style="height: auto;">' . $field_value . '</div>';
        ob_start();?>
			<script>
				var <?= $uniqid ?> = new MediumEditor("#<?= $uniqid ?>", {
					placeholder: false
				});

                <?= $uniqid ?>.subscribe('editableInput', function (event, editable) {
					var content = $(editable).html();
					$('#<?= $uniqid ?>_textarea').val(content).trigger('MediumEditor.change');
				});
			</script>
		<?php
        $html .= ob_get_clean();

		return $script . $html;
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
    public function getUniqid()
    {
        return $this->uniqid;
    }

    /**
     * @param mixed $uniqid
     */
    public function setUniqid($uniqid)
    {
        $this->uniqid = $uniqid;
    }

}