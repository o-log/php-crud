<?php

namespace OLOG\CRUD;

class Pager
{
    static public function pageSizeFormFieldName($table_index_on_page){
        return 'table_' . $table_index_on_page . '_page_size';
    }

    static public function pageOffsetFormFieldName($table_index_on_page){
        return 'table_' . $table_index_on_page . '_' . 'page_offset';
    }

	static public function getPageOffset($table_index_on_page)
	{
		$page_offset = 0;
		if (array_key_exists(self::pageOffsetFormFieldName($table_index_on_page), $_REQUEST)) {
			$page_offset = intval($_REQUEST[self::pageOffsetFormFieldName($table_index_on_page)]);
			if ($page_offset < 0) {
				$page_offset = 0;
			}
		}

		return $page_offset;
	}

	static public function getPageSize($table_index_on_page, $default_page_size = 30)
	{
		$page_size = $default_page_size;
		if (array_key_exists(self::pageSizeFormFieldName($table_index_on_page), $_REQUEST)) {
			$page_size = intval($_REQUEST[self::pageSizeFormFieldName($table_index_on_page)]);
			if ($page_size < 1) {
				return $default_page_size;
			}
			if ($page_size > 1000) {
				return $default_page_size;
			}
		}

		return $page_size;
	}

	static public function getNextPageStart($table_index_on_page)
	{
		$start = self::getPageOffset($table_index_on_page);
		$page_size = self::getPageSize($table_index_on_page);
		return $start + $page_size;
	}

	static public function getPrevPageStart($table_index_on_page)
	{
		$start = self::getPageOffset($table_index_on_page);
		$page_size = self::getPageSize($table_index_on_page);
		return $start - $page_size;
	}

	static public function hasPrevPage($table_index_on_page)
	{
		$start = self::getPageOffset($table_index_on_page);

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
	static public function renderPager($table_index_on_page, $elements_count = null, $display_total_rows_count = false, $total_rows_count = 0)
	{
		$pager_needed = false;
		if (self::hasPrevPage($table_index_on_page)){
			$pager_needed = true;
		}

		if (is_null($elements_count) || self::hasNextPage($table_index_on_page, $elements_count) || $display_total_rows_count){
			$pager_needed = true;
		}

        $html = '<ul class="pagination" data-page-size="' . self::getPageSize($table_index_on_page) . '" data-page-offset="' . self::getPageOffset($table_index_on_page) . '">';

		if ($pager_needed) {
			// TODO: looses existing get form
			$page_url = \OLOG\Url::path();

			if (self::hasPrevPage($table_index_on_page)) {
				$html .= '<li><a data-page-offset="0" href="' . $page_url . '?' . self::pageOffsetFormFieldName($table_index_on_page) . '=0&' . self::pageSizeFormFieldName($table_index_on_page) . '=' . self::getPageSize($table_index_on_page).'"><span class="glyphicon glyphicon-home"></span> 0-' . self::getPageSize($table_index_on_page) . '</a></li>';
				$html .= '<li><a data-page-offset="' . self::getPrevPageStart($table_index_on_page) . '" href="' . $page_url . '?' . self::pageOffsetFormFieldName($table_index_on_page) . '=' . self::getPrevPageStart($table_index_on_page) . '&' . self::pageSizeFormFieldName($table_index_on_page) . '='.self::getPageSize($table_index_on_page).'"><span class="glyphicon glyphicon-arrow-left"></span> ' . self::getPrevPageStart($table_index_on_page) . '-' . (self::getPrevPageStart($table_index_on_page) + self::getPageSize($table_index_on_page)) . '</a></li>';
			} else {
				$html .= '<li class="disabled"><a href="#"><span class="glyphicon glyphicon-home"></span></a></li>';
				$html .= '<li class="disabled"><a href="#"><span class="glyphicon glyphicon-arrow-left"></span></a></li>';
			}

			$html .= '<li class="active"><a data-page-offset="' . self::getPageOffset($table_index_on_page) . '" href="#">' . self::getPageOffset($table_index_on_page) . '-' . (self::getPageOffset($table_index_on_page) + self::getPageSize($table_index_on_page)) . '</a></li>';

			if (!$elements_count || self::hasNextPage($table_index_on_page, $elements_count)) {
				$html .= '<li><a data-page-offset="' . self::getNextPageStart($table_index_on_page) . '" class="next-page" href="' . $page_url . '?' . self::pageOffsetFormFieldName($table_index_on_page) . '=' . self::getNextPageStart($table_index_on_page) . '&' . self::pageSizeFormFieldName($table_index_on_page) . '=' . self::getPageSize($table_index_on_page) . '">' . self::getNextPageStart($table_index_on_page) . '-' . (self::getNextPageStart($table_index_on_page) + self::getPageSize($table_index_on_page)) . ' <span class="glyphicon glyphicon-arrow-right"></span></a></a></li>';
			} else {
				$html .= '<li class="disabled"><a href="#"><span class="glyphicon glyphicon-arrow-right"></span></a></li>';
			}

	        if ($display_total_rows_count) {
	            $html .= '<li class="disabled"><a href="#">Всего записей: ' . $total_rows_count . '</a></li>';
	        }
		}

		$html .= "</ul>";

		return $html;
	}

	/**
	 * @param $elements_count int Количество элементов на текущей странице. Если меньше размера страницы - значит, следующей страницы нет. Если null - значит оно не передано (т.е. неизвестно), при этом считаем что следующая страница есть.
	 * @return bool
	 */
	static public function hasNextPage($table_index_on_page, $elements_count)
	{
		if (is_null($elements_count)){
			return true;
		}

		$page_size = self::getPageSize($table_index_on_page);

		if ($elements_count < $page_size) {
			return false;
		}

		return true;
	}
}