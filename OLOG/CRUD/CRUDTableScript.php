<?php

namespace OLOG\CRUD;

class CRUDTableScript
{
	public static function render($table_class, $query_url)
	{
		static $include_script;

		if (!isset($include_script)) {
			$include_script = false;

			echo \OLOG\Preloader::preloaderJsHtml();
			?>
			<script>
				var CRUD = CRUD || {};

				CRUD.Table = CRUD.Table || function (container_class, url) {

						this.url = url;
						this.container_class = container_class;

						/**
						 * Классы
						 */
						const filter_class = '.filters-form';
						const table_class = '.table';
						const pagination_class = '.js-pagination';
						const options_editor_class = '.js-options-editor';
						const text_editor_class = '.js-text-editor';

						this.getContainerJqueryObj = function () {
							return $('.' + this.container_class);
						};

						this.getFilterJqueryObj = function () {
							return this.getContainerJqueryObj().find(filter_class);
						};

						this.getTableJqueryObj = function () {
							return this.getContainerJqueryObj().find(table_class);
						};

						this.getPaginationJqueryObj = function () {
							return this.getContainerJqueryObj().find(pagination_class);
						};

						this.init = function () {
							this.initFilter();
							this.initPagination();
							this.initOptionsEditor();
							this.initTextEditor();
							this.initClickRow();
						};

						this.reInit = function () {
							this.initClickRow();
						};

						this.initFilter = function () {
							var _this = this;

							this.getFilterJqueryObj().on('submit', function (e) {
								e.preventDefault();
								e.stopPropagation();

								// Устанавливаем параметр сдвига на 0 в контейнер
								_this.getPaginationJqueryObj().data('page-offset', 0);
								_this.ajaxRequest();
							});
						};

						this.initPagination = function () {
							var _this = this;

							this.getPaginationJqueryObj().on('click', 'a', function (e) {
								e.preventDefault();
								e.stopPropagation();

								if ($(this).attr('href') == "#") {
									return false;
								}

								// Устанавливаем параметр сдвига нажатой ссылки в контейнер
								_this.getPaginationJqueryObj().data('page-offset', $(this).data('page-offset'));
								_this.ajaxRequest();
							})
						};

						this.initOptionsEditor = function () {
							var _this = this;

							this.getTableJqueryObj().on('submit', options_editor_class, function (e) {
								e.preventDefault();
								e.stopPropagation();

								_this.ajaxRequest($(this).serializeArray());
							}).on('click', options_editor_class + ' > button', function (e) {
								$(this).closest('form').find('input[name="' + $(this).attr('name') + '"]').val($(this).attr('value'));
							});
						};

                        this.initTextEditor = function () {
                            var _this = this;

                            this.getTableJqueryObj().on('submit', text_editor_class, function (e) {
                                e.preventDefault();
                                e.stopPropagation();

                                _this.ajaxRequest($(this).serializeArray());
                            }).on('click', text_editor_class + ' > button', function (e) {
                                $(this).closest('form').find('input[name="' + $(this).attr('name') + '"]').val($(this).attr('value'));
                            });
                        };

						this.initClickRow = function () {
							var _this = this;

							this.getTableJqueryObj().find("> tbody > tr").each(function () {
								var $tr = $(this);
								// Проверка на наличие ссылки
								if ($tr.find("a").length == 0) {
									return false;
								}
								// Проверка на наличие только одной ссылки
								if ( ($tr.find("a").length > 1)) {
									return false;
								}
								var $link = $tr.find("a:first");
								var url = $link.attr("href");
								var link_style = "z-index: 1;position: absolute;top: 0;bottom: 0;left: 0;right: 0;display: block;";
								$tr.find("> td").each(function () {
									var $td = $(this).css({"position":"relative"});
									if ($td.find("> *").prop("tagName") != "FORM") {
										$td.prepend('<a href="' + url + '" style="' + link_style + '"></a>');
									}
								});
							});
						};

						/**
						 * Перерисовка таблици и пагинации
						 * @param received_html
						 */
						this.tableRender = function (received_html) {
							var $box = $('<div>', {html: received_html});

							this.getTableJqueryObj().html($box.find('.' + this.container_class).find(table_class).html());
							this.getPaginationJqueryObj().html($box.find('.' + this.container_class).find(pagination_class).html());

							this.reInit();
						};

						/**
						 * Ajax запрос
						 * @array data
						 */
						this.ajaxRequest = function (data) {
							var _this = this;
							var data = data || [];

							var filter_arr = this.getFilterJqueryObj().serializeArray();
							var pagination_arr = [
								{
									'name': 'table_' + this.container_class + '_page_offset',
									'value': this.getPaginationJqueryObj().data('page-offset')
								},
								{
									'name': 'table_' + this.container_class + '_page_size',
									'value': this.getPaginationJqueryObj().data('page-size')
								}
							];

							$.merge(data, filter_arr);
							$.merge(data, pagination_arr);

							$.ajax({
								type: "POST",
								url: this.url,
								data: data,
								beforeSend: function () {
									OLOG.preloader.show();
								},
								complete: function () {
									OLOG.preloader.hide();
								},
								success: function (received_html) {
									_this.tableRender(received_html);
								}
							});
						};

						/**
						 * Начальный запуск
						 */
						this.init();
					}
			</script>
			<?php
		}

		?>
		<script>
			new CRUD.Table('<?= $table_class ?>', '<?= $query_url ?>');
		</script>
		<?php
	}

	public static function getHtml($table_class, $query_url)
	{
		ob_start();
		self::render($table_class, $query_url);
		return ob_get_clean();
	}
}