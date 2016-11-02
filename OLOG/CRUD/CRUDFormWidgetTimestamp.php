<?php

namespace OLOG\CRUD;

use OLOG\Sanitize;

class CRUDFormWidgetTimestamp implements InterfaceCRUDFormWidget
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
		static $CRUDFormWidgetTimestamp_include_script;

		$field_name = $this->getFieldName();
		$field_value = CRUDFieldsAccess::getObjectFieldValue($obj, $field_name);

		/* Нужно изменить на нах CDN */
		$script = '';
		if (!isset($CRUDFormWidgetTimestamp_include_script)) {
			$script = '
								<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.2/moment.min.js"></script>
								<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.2/locale/ru.js"></script>
				<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.43/css/bootstrap-datetimepicker.min.css">
								<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.43/js/bootstrap-datetimepicker.min.js"></script>
			';
			$CRUDFormWidgetTimestamp_include_script = false;
		}

		$is_required_str = '';
		if ($this->is_required) {
			$is_required_str = ' required ';
		}

		$field_value_attr = '';
		if ($field_value) {
			$field_value_attr = date('d-m-Y H:i:s', $field_value);
		}

		$uniqid = uniqid('CRUDFormWidgetTimestamp_');
		$input_cols = $this->getShowNullCheckbox() ? '10' : '12';

		$html = '';
		$html .= '<div class="row">';
		$html .= '<div class="col-sm-' . $input_cols . '">';

		ob_start(); ?>
		<input type="hidden" id="<?= $uniqid ?>_input" name="<?= Sanitize::sanitizeAttrValue($field_name) ?>" value="<?= Sanitize::sanitizeTagContent($field_value) ?>" data-field="<?= $uniqid ?>_date" <?= $is_required_str ?>>
		<div class="input-group date" id="<?= $uniqid ?>">
			<input id="<?= $uniqid ?>_date" type="text" class="form-control" value="<?= $field_value_attr ?>">
			<span class="input-group-addon">
	                <span class="glyphicon glyphicon-calendar"></span>
	            </span>
		</div>
		<script>
			$("#<?= $uniqid ?>").datetimepicker({
				format: "DD-MM-YYYY HH:mm:ss",
				sideBySide: true,
				showTodayButton: true
			}).on("dp.change", function (obj) {
				if (obj.date) {
					$("#<?= $uniqid ?>_input").val(obj.date.unix()).trigger('change');
				} else {
					$("#<?= $uniqid ?>_input").val('').trigger('change');
				}
			}).on('dp.update', function () {
				$(this).datepicker('dp.hide');
			});
		</script>
		<?php
		$html .= ob_get_clean();
		$html .= '</div>';

		if ($this->getShowNullCheckbox()) {

			$is_null_checked = '';
			if (is_null($field_value)) {
				$is_null_checked = ' checked ';
			}
			ob_start(); ?>
			<div class="col-sm-2">
				<label class="form-control-static">
					<input id="<?= $uniqid ?>___is_null" type="checkbox" value="1" name="<?= Sanitize::sanitizeAttrValue($field_name) ?>___is_null" <?= $is_null_checked ?>> NULL
				</label>
			</div>
			<script>
				(function () {
					var $input_is_null = $('#<?= $uniqid ?>___is_null');

					$("#<?= $uniqid ?>_input").on('change', function () {
						$input_is_null.prop('checked',false);
					});

					$input_is_null.on('change', function () {
						if ($(this).is(':checked')) {
							$('#<?= $uniqid ?>').data("DateTimePicker").clear();
							$('#<?= $uniqid ?>_input').val('');
						}
					});
				})();
			</script>
			<?php
			$html .= ob_get_clean();
		}

		$html .= '</div>';

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