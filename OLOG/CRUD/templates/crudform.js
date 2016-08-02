var CRUD = CRUD || {};

CRUD.Form = CRUD.Form || {

		init: function (form_element_id) {
			var $form = $('#' + form_element_id);
			CRUD.Form.required(form_element_id);
			$form.on('submit', function (e) {
				if (CRUD.Form.validator(form_element_id) == true) {
				} else {
					e.preventDefault();
					CRUD.Form.errors(CRUD.Form.validator(form_element_id));
				}
			});
		},

		required: function (form_element_id) {
			var $form = $('#' + form_element_id);
			var required_class = 'required-class';
			$form.find('[required]').each(function () {
				var $this = $(this);
				var $field = ($this.data('field')) ? $('#' + $this.data('field')) : $this;
				$this.on('change keyup blur', function () {
					if (CRUD.Form.validator(form_element_id, $this) == true) {
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

		validator: function (form_element_id, $required_elem) {
			var $form = $('#' + form_element_id);
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
						if ($.inArray($this.attr('name'), errors) < 0 ) {
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