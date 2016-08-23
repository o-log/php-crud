var CRUD = CRUD || {};

CRUD.Table = CRUD.Table || {

        init: function (table_container_element_id, query_url) {

            CRUD.Table.clickTableRow(table_container_element_id);
            CRUD.Table.filterAjaxLoad(table_container_element_id, query_url);
            CRUD.Table.paginationAjaxLoad(table_container_element_id, query_url);
        },

        clickTableRow: function (table_container_element_id) {
            var table_elem_selector = '.' + table_container_element_id + ' .table';
            $(table_elem_selector).each(function () {
				$(this).find("tbody tr").each(function () {
					var $tr = $(this);
					// Проверка на наличие ссылки
					if ($tr.find("a").length == 0) {
						return false;
					}
					// Проверка на наличие только одной ссылки
					if ($tr.find("a").length > 1) {
						return false;
					}
					var $link = $tr.find("a:first");
					var url = $link.attr("href");
					var link_style = "z-index: 1;position: absolute;top: 0;bottom: 0;left: 0;right: 0;display: block;";
					$tr.find("td").each(function () {
						var $td = $(this).attr("style", "position: relative;");
						var $childrenTag = $td.find(">*");
						if ($childrenTag[0] && $childrenTag[0].tagName == "FORM") {
							return false;
						}
						$td.prepend('<a href="' + url + '" style="' + link_style + '"></a>');
					});
				});
			});
        },

        filterAjaxLoad: function (table_container_element_id, query_url) {
            var filter_elem_selector = '.' + table_container_element_id + ' .filters-form';
            var pagination_elem_selector = '.' + table_container_element_id + ' .pagination';
            $(filter_elem_selector).on('submit', function (e) {
                e.preventDefault();
                var params = $(this).serialize();
                $(this).data('params', params);
                var filters = $(this).data('params') || '';
                var pagination = $(pagination_elem_selector).data('params') || '';
                var query = query_url + '?' + filters + '&' + pagination;
                CRUD.Table.requestAjax(table_container_element_id, query);
            });
        },

        paginationAjaxLoad: function (table_container_element_id, query_url) {
            var filter_elem_selector = '.' + table_container_element_id + ' .filters-form';
            var pagination_elem_selector = '.' + table_container_element_id + ' .pagination';
            $(pagination_elem_selector).on('click', 'a', function (e) {
                e.preventDefault();
                if ($(this).attr('href') == "#") {return false;}
                var params = url('query', $(this).attr('href'));
                $(this).data('params', params);
                var filters = $(filter_elem_selector).data('params') || '';
                var pagination = $(this).data('params') || '';
                var query = query_url + '?' + filters + '&' + pagination;
                CRUD.Table.requestAjax(table_container_element_id, query);
            });
        },

        requestAjax: function (table_container_element_id, query) {
            var table_elem_selector = '.' + table_container_element_id + ' .table';
            var pagination_elem_selector = '.' + table_container_element_id + ' .pagination';


            $.ajax({
                url: query
            }).success(function (received_html) {
                var received_table_html = $(received_html).find(table_elem_selector).html();
                $(table_elem_selector).html(received_table_html);

                $(pagination_elem_selector).html($(received_html).find(pagination_elem_selector).html());

                CRUD.Table.clickTableRow(table_container_element_id);
            });
        }
    };
