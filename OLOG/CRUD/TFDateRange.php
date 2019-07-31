<?php
declare(strict_types=1);

namespace OLOG\CRUD;

use OLOG\HTML;
use OLOG\REQUEST;

class TFDateRange implements TFInterface
{
    private $title;
    private $field_name;
    private $filter_iniq_id;
    private $placeholder_start;
    private $placeholder_end;

    private function getStartDateValueFromForm()
    {
        return REQUEST::optional($this->getStartFilterId());
    }

    private function getEndDateValueFromForm()
    {
        return REQUEST::optional($this->getEndFilterId());
    }

    private function getStartFilterId()
    {
        return $this->getFilterIniqId() . '_start_date';
    }

    private function getEndFilterId()
    {
        return $this->getFilterIniqId() . '_end_date';
    }

    static public function generateInputHtml(string $filterId, string $placeholder): string
    {
        $html = '';
        $html .= '<input type="hidden" id="' . $filterId . '_input" name="' . $filterId . '"  data-field="' . $filterId . '_date"/>';
        $html .= '<span id="' . $filterId . '">';
        $html .= '<input placeholder="' . $placeholder . '" id="' . $filterId . '_date" type="date" class="form-control form-control-sm" value=""/>';
        $html .= '</span>';


//        $html .= '<script>
//			$("#' . $filterId . '_date").datetimepicker({
//				format: "DD-MM-YYYY HH:mm:ss",
//				sideBySide: true,
//				showTodayButton: true
//			}).on("dp.change", function (obj) {
//				if (obj.date) {
//					$("#' . $filterId . '_input").val(obj.date.format("YYYY-MM-DD HH:mm:ss")).trigger("change");
//				} else {
//					$("#' . $filterId . '_input").val("").trigger("change");
//				}
//			});
//		</script>';

        return $html;
    }

    public function getHtml()
    {
        $html = '';

//        $html = '
//								<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.2/moment.min.js"></script>
//								<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.2/locale/ru.js"></script>
//				<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.43/css/bootstrap-datetimepicker.min.css">
//								<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.43/js/bootstrap-datetimepicker.min.js"></script>
//			';

        $html .= self::generateInputHtml($this->getStartFilterId(), $this->getPlaceholderStart());
        $html .= self::generateInputHtml($this->getEndFilterId(), $this->getPlaceholderEnd());

        ob_start();
        ?>
        <script>
            var CRUDTableFilterDateRange = function (elem_id, hidden_input_el_id) {
                var $input = $('#' + elem_id);
                var timer;
                var value = $input.val();
                var $hidden_input = $('#' + hidden_input_el_id);

                $input.on('focusout', function (e) {
                    var $this = $(this);

                    if ((value == $this.val()) && (e.type != 'paste')) {
                        return;
                    }

                    value = $this.val();
                    console.log(value);

					$hidden_input.val(value);

                    clearTimeout(timer);
                    timer = setTimeout(function () {
                        $this.closest('form').trigger('submit');
                    }, 200);
                });
            };
            new CRUDTableFilterDateRange('<?= $this->getStartFilterId() . '_date' ?>', '<?= $this->getStartFilterId() . '_input' ?>');
            new CRUDTableFilterDateRange('<?= $this->getEndFilterId() . '_date' ?>', '<?= $this->getEndFilterId() . '_input' ?>');

        </script>
        <?php
        $html .= ob_get_clean();

        return $html;
    }

    /**
     * Возвращает пару из sql-условия и массива значений плейсхолдеров. Массив значений может быть пустой если плейсхолдеры не нужны.
     * @return array
     */
    public function sqlConditionAndPlaceholderValue()
    {
        $where = [];
        $placeholder_values_arr = [];

        // для этого виджета галка включения не выводится: если в поле пустая строка - он игрорируется

        $start = strtotime($this->getStartDateValueFromForm());
        $end = strtotime($this->getEndDateValueFromForm());

        $column_name = $this->getFieldName();
        $column_name = preg_replace("/[^a-zA-Z0-9_]+/", "", $column_name);

        if ($start != '') {
            $where[] = ' ' . $column_name . ' >= ? ';
            $placeholder_values_arr[] = $start;
        }

        if ($end != '') {
            $where[] = ' ' . $column_name . ' <= ? ';
            $placeholder_values_arr[] = $end;
        }

        return [implode(' and ', $where), $placeholder_values_arr];
    }

    public function __construct($filter_uniq_id, $title, $field_name, $placeholder_start = '', $placeholder_end = '')
    {
        $this->setFilterIniqId($filter_uniq_id);
        $this->setTitle($title);
        $this->setFieldName($field_name);
        $this->setPlaceholderStart($placeholder_start);
        $this->setPlaceholderEnd($placeholder_end);
    }

    public function getFilterIniqId()
    {
        return $this->filter_iniq_id;
    }

    public function setFilterIniqId($filter_iniq_id)
    {
        $this->filter_iniq_id = $filter_iniq_id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getFieldName()
    {
        return $this->field_name;
    }

    public function setFieldName($field_name)
    {
        $this->field_name = $field_name;
    }

    public function setPlaceholderStart($placeholder_start)
    {
        $this->placeholder_start = $placeholder_start;
    }

    public function setPlaceholderEnd($placeholder_end)
    {
        $this->placeholder_end = $placeholder_end;
    }

    public function getPlaceholderStart()
    {
        return $this->placeholder_start;
    }

    public function getPlaceholderEnd()
    {
        return $this->placeholder_end;
    }
}
