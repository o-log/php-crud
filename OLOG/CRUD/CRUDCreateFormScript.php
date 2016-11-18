<?php

namespace OLOG\CRUD;

class CRUDCreateFormScript
{
	public static function render($form_id, $table_class)
	{
		static $include_script;

		if (!isset($include_script)) {
			$include_script = false;

			echo \OLOG\Preloader::preloaderJsHtml();
			?>
			<script>
				var CRUD = CRUD || {};

				CRUD.CreateForm = CRUD.CreateForm || {

						init: function (form_elem, table_elem) {
							var $form = $(form_elem);

							$form.on('submit', function (e) {
								e.preventDefault();
								var url = $form.attr('action');
								var data = $form.serializeArray();

								CRUD.CreateForm.requestAjax(table_elem, url, data);
							});
						},

						requestAjax: function (table_elem, query, data) {

							OLOG.preloader.show();

							$.ajax({
								type: "POST",
								url: query,
								data: data
							}).success(function (received_html) {
								OLOG.preloader.hide();
								
								var $box = $('<div>', {html: received_html});
								$(table_elem).html($box.find(table_elem).html());
							}).fail(function () {
								OLOG.preloader.hide();
							});
						}
					};
			</script>
			<?php
		}

		?>
		<script>
			CRUD.CreateForm.init('#<?= $form_id ?>', '.<?= $table_class ?>');
		</script>
		<?php
	}

	public static function getHtml($form_id, $table_class)
	{
		ob_start();
		self::render($form_id, $table_class);
		return ob_get_clean();
	}
}