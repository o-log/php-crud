<?php

namespace OLOG\CRUD;

class CInternalFScript
{
	public static function render($form_id)
	{
		static $include_script;

		if (!isset($include_script)) {
			$include_script = false;

			echo \OLOG\Preloader::preloaderJsHtml();
			?>
			<style>
				.required-class {border: 1px solid red;}
				.required-class[type="radio"]:before {font-size: 1em;content: '*';color: red;}
			</style>
			<script>
				var CRUD = CRUD || {};

				CRUD.Form = CRUD.Form || {

						init: function (form_id) {
							var $form = $('#' + form_id);
							CRUD.Form.required(form_id);
							$form.find('[type="submit"]').on('click', function (e) {
								if (CRUD.Form.validator(form_id) == true) {
								} else {
									e.preventDefault();
									CRUD.Form.errors(CRUD.Form.validator(form_id));
								}
							});
						},

						required: function (form_id) {
							var $form = $('#' + form_id);
							var required_class = 'required-class';
							$form.find('[required]').each(function () {
								var $this = $(this);
								var $field = ($this.data('field')) ? $('#' + $this.data('field')) : $this;
								$this.on('change keyup blur', function () {
									if (CRUD.Form.validator(form_id, $this) == true) {
										if ($this.attr('type') != 'radio') {
											$field.removeClass(required_class);
										} else {
											var radio_name = $this.attr('name');
											$form.find('[name="' + radio_name + '"]').removeClass(required_class);
										}
									} else {
										if ($this.attr('type') != 'radio') {
											$field.addClass(required_class);
										} else {
											var radio_name = $this.attr('name');
											$form.find('[name="' + radio_name + '"]').addClass(required_class);
										}
									}
								}).trigger('change');
							});
						},

						validator: function (form_id, $required_elem) {
							var $form = $('#' + form_id);
							var $required = $required_elem || '[required]';
							var errors = [];
							$form.find($required).each(function () {
								var $this = $(this);
								if ($this.attr('type') != 'radio') {
									if ($this.val() == '') {
										errors.push($this.attr('name'));
									}
								} else {
									var radio_name = $this.attr('name');
									if ($form.find('[name="' + radio_name + '"]:checked').length == 0) {
										if ($.inArray($this.attr('name'), errors) < 0) {
											errors.push($this.attr('name'));
										}
									}
								}
							});
							if (errors.length == 0) {
								return true;
							} else {
								return errors;
							}
						},

						errors: function (errors) {
							alert('Нужно заполнить поля:\n - ' + errors.join('\n - '));
						}

					};
			</script>
			<?php
		}

		?>
		<script>
			CRUD.Form.init('<?= $form_id ?>');
		</script>
		<?php
	}

	public static function getHtml($form_id)
	{
		ob_start();
		self::render($form_id);
		return ob_get_clean();
	}
}