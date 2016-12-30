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

				CRUD.Table = CRUD.Table || {

						data: {},

						init: function (table_class, url) {
							CRUD.Table.data[table_class] = {};
							CRUD.Table.data[table_class].ajax_url = url;

							CRUD.Table.clickTableRow(table_class);
							CRUD.Table.filterAjaxLoad(table_class);
							CRUD.Table.paginationAjaxLoad(table_class);
							CRUD.Table.editTableData(table_class);
						},

						clickTableRow: function (table_class) {
							var table_elem_selector = '.' + table_class + ' .table';
							$(table_elem_selector).each(function () {
								$(this).find("tbody tr").each(function () {
									var $tr = $(this);
									// Проверка на наличие ссылки
									if ($tr.find("a").length == 0) {
										return false;
									}
									// Проверка на наличие только одной ссылки
									if ( ($tr.find("a").length > 1) || ($tr.find('.js-options-editor').length > 0) ) {
										return false;
									}
									var $link = $tr.find("a:first");
									var url = $link.attr("href");
									var link_style = "z-index: 1;position: absolute;top: 0;bottom: 0;left: 0;right: 0;display: block;";
									$tr.find("td").each(function () {
										var $td = $(this).css({"position":"relative"});
										var $childrenTag = $td.find(">*");
										if ($childrenTag[0] && $childrenTag[0].tagName == "FORM") {
											return false;
										}
										$td.prepend('<a href="' + url + '" style="' + link_style + '"></a>');
									});
								});
							});
						},

						filterAjaxLoad: function (table_class) {
							var filter_elem_selector = '.' + table_class + ' .filters-form';
							var pagination_elem_selector = '.' + table_class + ' .pagination';
							$(filter_elem_selector).on('submit', function (e) {
								e.preventDefault();
								e.stopPropagation();
								CRUD.Table.data[table_class].filter = $(this).serialize();
								CRUD.Table.data[table_class].pagination = '';
								CRUD.Table.getRequeat(table_class);
							});
						},

						paginationAjaxLoad: function (table_class) {
							var filter_elem_selector = '.' + table_class + ' .filters-form';
							var pagination_elem_selector = '.' + table_class + ' .pagination';
							$(pagination_elem_selector).on('click', 'a', function (e) {
								e.preventDefault();
								if ($(this).attr('href') == "#") {
									return false;
								}
								CRUD.Table.data[table_class].pagination = $(this).attr('href').split('?')[1];
								CRUD.Table.getRequeat(table_class);
							});
						},

						tableRender: function (table_class, received_html) {
							var table_elem_selector = '.' + table_class + ' .table';
							var pagination_elem_selector = '.' + table_class + ' .pagination';
							var $box = $('<div>', {html: received_html});

							$(table_elem_selector).html($box.find(table_elem_selector).html());
							$(pagination_elem_selector).html($box.find(pagination_elem_selector).html());

							CRUD.Table.clickTableRow(table_class);
						},

						getAjaxUrl: function (table_class) {
							var ajax_url = CRUD.Table.data[table_class].ajax_url;

							if (CRUD.Table.data[table_class].filter || CRUD.Table.data[table_class].pagination) {
								ajax_url += '?';
							}

							if (CRUD.Table.data[table_class].filter) {
								ajax_url += CRUD.Table.data[table_class].filter;
								if (CRUD.Table.data[table_class].pagination) {
									ajax_url += '&';
								}
							}

							if (CRUD.Table.data[table_class].pagination) {
								ajax_url += CRUD.Table.data[table_class].pagination;
							}

							return ajax_url;
						},

						getRequeat: function (table_class) {
							$.ajax({
								url: CRUD.Table.getAjaxUrl(table_class),
								dataType: 'html',
								beforeSend: function () {
									OLOG.preloader.show();
								},
								complete: function () {
									OLOG.preloader.hide();
								},
								success: function (received_html) {
									CRUD.Table.tableRender(table_class, received_html);
								}
							});
						},

						editTableData: function (table_class) {
							// навешиваем обработчик на всю таблицу, чтобы он не пострадал при перезагрузке контента таблицы аяксом
							$('.' + table_class).on('submit', '.js-options-editor', function (e) {
								e.preventDefault();
								e.stopPropagation();

								$.ajax({
									url: CRUD.Table.getAjaxUrl(table_class),
									type: "post",
									data: $(this).serializeArray(),
									beforeSend: function () {
										OLOG.preloader.show();
									},
									complete: function () {
										OLOG.preloader.hide();
									},
									success: function (received_html) {
										CRUD.Table.tableRender(table_class, received_html);
									}
								});
							}).on('click', '.js-options-editor > button', function (e) {
								$(this).nextAll('input[name="' + $(this).attr('name') + '"]').val($(this).attr('value'));
							});
						}

					};


				CRUD.newTable = CRUD.newTable || function ($container, url) {

						this.url = url;
						this.$container = $container;
						this.table_class = '.table';
						this.$table = this.$container.find(this.table_class);
						this.filter_class = '.filters-form';
						this.$filter = this.$container.find(this.filter_class);
						this.pagination_class = '.pagination';
						this.$pagination = this.$container.find(this.pagination_class);
						this.options_editor_class = '.js-options-editor';

						this.init = function () {
							this.initFilter();
							this.initPagination();
							this.initEditor();
							this.initClickRow();
						};

						this.reInit = function () {

						};

						this.initFilter = function () {
							var _this = this;

							this.$filter.on('submit', function (e) {
								e.preventDefault();
								e.stopPropagation();

								// Устанавливаем параметр сдвига на 0 в контейнер
								_this.$pagination.data('page-offset', 0);
								_this.ajaxRequest();
							});
						};

						this.initPagination = function () {
							var _this = this;

							this.$pagination.on('click', 'a', function (e) {
								e.preventDefault();
								e.stopPropagation();

								if ($(this).attr('href') == "#") {
									return false;
								}

								// Устанавливаем параметр сдвига нажатой ссылки в контейнер
								_this.$pagination.data('page-offset', $(this).data('page-offset'));
								_this.ajaxRequest();
							})
						};

						this.initEditor = function () {
							var _this = this;

							this.$table.on('submit', 'form', function (e) {
								e.preventDefault();
								e.stopPropagation();

								_this.ajaxRequest($(this).serializeArray());
							}).on('click', this.options_editor_class + ' > button', function (e) {
								$(this).nextAll('input[name="' + $(this).attr('name') + '"]').val($(this).attr('value'));
							});
						};

						this.initClickRow = function () {
							var _this = this;

							this.$table.find("> tbody > tr").each(function () {
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

							this.$table.html($box.find(this.table_class).html());
							this.$pagination.html($box.find(this.pagination_class).html());
						};

						/**
						 * Ajax запрос
						 * @array data
						 */
						this.ajaxRequest = function (data) {
							var _this = this;
							var data = data || [];

							var filter_arr = this.$filter.serializeArray();
							var pagination_arr = [
								{
									'name': '<?= Pager::pageOffsetFormFieldName($table_class) ?>',
									'value': this.$pagination.data('page-offset')
								},
								{
									'name': '<?= Pager::pageSizeFormFieldName($table_class) ?>',
									'value': this.$pagination.data('page-size')
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
			//CRUD.Table.init('<?= $table_class ?>', '<?= $query_url ?>');

			new CRUD.newTable($('.<?= $table_class ?>'), '<?= $query_url ?>');
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