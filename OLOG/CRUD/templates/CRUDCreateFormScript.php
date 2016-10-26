<?php

namespace OLOG\CRUD\templates;

class CRUDCreateFormScript
{
	public static function render()
	{
		?>
		<script>
			var CRUD = CRUD || {};

			CRUD.CreateForm = CRUD.CreateForm || {

					init: function (form_elem, table_elem) {
						var $table = $(table_elem);
						var $form = $(form_elem);

						$form.on('submit', function (e) {
							e.preventDefault();
							var url = $form.attr('action');
							var data = $form.serializeArray();

							CRUD.CreateForm.requestAjax(table_elem, url, data);
						});

						console.log($table, $form);
					},

					requestAjax: function (table_elem, query, data) {

						CRUD.Table.preloader.show();

						$.ajax({
							type: "POST",
							url: query,
							data: data
						}).success(function (received_html) {
							CRUD.Table.preloader.hide();

							var received_table_html = $(received_html).find(table_elem).html();
							$(table_elem).html(received_table_html);
						}).fail(function () {
							CRUD.Table.preloader.hide();
						});
					}
				};
		</script>
		<?php
	}
}