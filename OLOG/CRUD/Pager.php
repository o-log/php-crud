<?php

namespace OLOG\CRUD;

class Pager
{
    static public function getPageOffset()
    {
        $page_offset = 0;
        if (array_key_exists('page_offset', $_GET)) {
            $page_offset = intval($_GET['page_offset']);
            if ($page_offset < 0) {
                $page_offset = 0;
            }
        }

        return $page_offset;
    }

    static public function getPageNumber()
    {
        $page_number = 1;
        if (array_key_exists('page_number', $_GET)) {
            $page_number = intval($_GET['page_number']);
            if ($page_number < 1) {
                return 1;
            }
        }

        return $page_number;
    }

    static public function getPageSize($default_page_size = 30)
    {
        $page_size = $default_page_size;
        if (array_key_exists('page_size', $_GET)) {
            $page_size = intval($_GET['page_size']);
            if ($page_size < 1) {
                return $default_page_size;
            }
            if ($page_size > 1000) {
                return $default_page_size;
            }
        }

        return $page_size;
    }

    static public function getNextPageStart()
    {
        $start = self::getPageOffset();
        $page_size = self::getPageSize();
        return $start + $page_size;
    }

    static public function getPrevPageStart()
    {
        $start = self::getPageOffset();
        $page_size = self::getPageSize();
        return $start - $page_size;
    }

    static public function hasPrevPage()
    {
        $start = self::getPageOffset();

        if ($start > 0) {
            return true;
        }

        return false;
    }

    /**
     * "Дальше" рисуется всегда, если параметр $elements_count не передан
     *
     * @param int $elements_count
     * @return string
     */
    static public function renderPager($elements_count = null, $table_container_element_id)
    {
        $pager_needed = false;
        if (self::hasPrevPage()){
            $pager_needed = true;
        }

        if (is_null($elements_count) || self::hasNextPage($elements_count)){
            $pager_needed = true;
        }

        if (!$pager_needed) {
            return '';
        }

		$pagination_element_id = 'pagination_' . rand(1, 999999);

        $html = "<ul class='pagination' id='" . $pagination_element_id . "'>";

        // TODO: looses existing get form
        $page_url = \OLOG\Url::getCurrentUrlNoGetForm();

        if (self::hasPrevPage()) {
            $html .= '<li><a href="' . $page_url . '?page_offset=0&page_size='.self::getPageSize().'"><span class="glyphicon glyphicon-home"></span> 0-' . self::getPageSize() . '</a></li>';
            $html .= '<li><a href="' . $page_url . '?page_offset=' . self::getPrevPageStart() . '&page_size='.self::getPageSize().'"><span class="glyphicon glyphicon-arrow-left"></span> ' . self::getPrevPageStart() . '-' . (self::getPrevPageStart() + self::getPageSize()) . '</a></li>';
        } else {
            $html .= '<li class="disabled"><a href="#"><span class="glyphicon glyphicon-home"></span></a></li>';
            $html .= '<li class="disabled"><a href="#"><span class="glyphicon glyphicon-arrow-left"></span></a></li>';
        }

        $html .= "<li class='active'><a href='#'>" . self::getPageOffset() . '-' . (self::getPageOffset() + self::getPageSize()) . '</a></li>';

        if (!$elements_count || self::hasNextPage($elements_count)) {
            $html .= "<li><a class='next-page' href='" . $page_url . "?page_offset=" . self::getNextPageStart() . "&page_size=".self::getPageSize()."'>" . self::getNextPageStart() . "-" . (self::getNextPageStart() + self::getPageSize()) . ' <span class="glyphicon glyphicon-arrow-right"></span></a></a></li>';
        } else {
            $html .= '<li class="disabled"><a href="#"><span class="glyphicon glyphicon-arrow-right"></span></a></li>';
        }

        $html .= "</ul>";

		ob_start();?>

		<script>
			(function () {
				var pagination = $('#<?= $pagination_element_id ?>');
				var table_id = '<?= $table_container_element_id ?>';
				pagination.on('click', 'a', function (e) {
					e.preventDefault();
					var url = $(this).attr('href');
					if (url == "#") {return false;}
					$.ajax({
						url: url
					}).success(function(received_html) {
						$('#'+table_id).find('> .table').html($(received_html).find('#'+table_id).find('> .table').html());
						$('#'+table_id).find('> .pagination').html($(received_html).find('#'+table_id).find('> .pagination').html());
					});
				});
			})();
		</script>

		<?php
		$html .= ob_get_clean();

        return $html;
    }

    /**
     * @param $elements_count int Количество элементов на текущей странице. Если меньше размера страницы - значит, следующей страницы нет. Если null - значит оно не передано (т.е. неизвестно), при этом считаем что следующая страница есть.
     * @return bool
     */
    static public function hasNextPage($elements_count)
    {
        if (is_null($elements_count)){
            return true;
        }

        $page_size = self::getPageSize();

        if ($elements_count < $page_size) {
            return false;
        }

        return true;
    }
}