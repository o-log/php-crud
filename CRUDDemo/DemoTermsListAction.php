<?php

namespace CRUDDemo;

use OLOG\BT\BT;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetRadios;
use OLOG\CRUD\CRUDFormWidgetOptions;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilterEqualInvisible;
use OLOG\CRUD\CRUDTableFilterEqualOptions;
use OLOG\CRUD\CRUDTableFilterEqualOptionsInline;
use OLOG\CRUD\CRUDTableFilterLike;
use OLOG\CRUD\CRUDTableFilterLikeInline;
use OLOG\CRUD\CRUDTableWidgetDelete;
use OLOG\CRUD\CRUDTableWidgetHtml;
use OLOG\CRUD\CRUDTableWidgetOptions;
use OLOG\CRUD\CRUDTableWidgetText;
use OLOG\CRUD\CRUDTableWidgetTextWithLink;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDTableWidgetWeight;
use OLOG\Operations;
use OLOG\Url;

class DemoTermsListAction
{
    static public function getUrl()
    {
        return '/terms';
    }

    public function action()
    {
        \OLOG\Exits::exit403If(!Auth::currentUserHasAnyOfPermissions([1]));

        $html = '';

        $table_id = '8726438755234';

        $html .= \OLOG\CRUD\CRUDTable::html(
            \CRUDDemo\DemoTerm::class,
            CRUDForm::html(
                new DemoTerm,
                [
                    new CRUDFormRow(
                        'Title',
                        new CRUDFormWidgetInput('title', false, true)
                    ),
                    new CRUDFormRow(
                        'Chooser',
                        new CRUDFormWidgetRadios('chooser', [
                            1 => 'one',
                            2 => 'two'
                        ], true, true)
                    ),
                    new CRUDFormRow(
                        'Options',
                        new CRUDFormWidgetOptions('options', [
                            1 => 'one',
                            2 => 'two'
                        ], false, true)
                    )
                ]
            ),
            [
                new CRUDTableColumn(
                    'Edit',
                    new CRUDTableWidgetTextWithLink(
                        '{this->title}',
                        DemoTermEditAction::getUrl('{this->id}')
                        )
                ),
	            new CRUDTableColumn(
		            'Vocabulary',
		            new CRUDTableWidgetOptions(
			            '{this->vocabulary_id}',
			            DemoTerm::VOCABULARIES_ARR
		            )
	            ),
                new CRUDTableColumn(
                    'Parent',
                    new CRUDTableWidgetText(
                        '{' . DemoTerm::class . '.{this->parent_id}->title}'
                    )
                ),
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetHtml('<form action="' . Url::getCurrentUrl() . '" class="editor-form">
                    <input type="hidden" name="' . Operations::FIELD_NAME_OPERATION_CODE . '" value="' . CRUDTable::OPERATION_UPDATE_MODEL_FIELD . '">
                    <input type="hidden" name="' . CRUDTable::FIELD_FIELD_NAME. '" value="vocabulary_id">
                    <input type="hidden" name="' . CRUDTable::FIELD_CRUDTABLE_ID . '" value="' . $table_id . '">
                    <input type="hidden" name="' . CRUDTable::FIELD_MODEL_ID . '" value="{this->id}">
                    <input name="' . CRUDTable::FIELD_FIELD_VALUE . '" value="{this->vocabulary_id}">
                    <input type="submit">
                    </form>
                    ')
                ),
                new CRUDTableColumn('', new CRUDTableWidgetWeight(['parent_id' => null])),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterEqualInvisible('parent_id', null),
                //new CRUDTableFilter('vocabulary_id', CRUDTableFilter::FILTER_EQUAL, DemoTerm::VOCABULARY_MAIN, new CRUDFormWidgetOptions('vocabulary_id', DemoTerm::VOCABULARIES_ARR)),
                new CRUDTableFilterEqualOptionsInline('34785ty8y45t8', 'Словарь', 'vocabulary_id', DemoTerm::VOCABULARIES_ARR, false, null, true),
	            new CRUDTableFilterEqualOptionsInline('345634g3tg534', '', 'gender', DemoTerm::GENDER_ARR, false, null, true, 'М. и Ж.'),
                //new CRUDTableFilter('title', CRUDTableFilter::FILTER_LIKE, '')
                new CRUDTableFilterLikeInline('3748t7t45gdfg', '', 'title', 'Название содержит')
            ],
            'weight',
            $table_id,
            CRUDTable::FILTERS_POSITION_INLINE,
            true
        );

        $html .= '
        <script>
            var query_url = "' . Url::getCurrentUrlNoGetForm(). '";
            var table_class = "' . $table_id . '";

            // навешиваем обработчик на всю таблицу, чтобы он не пострадал при перезагрузке контента таблицы аяксом
            $("." + table_class).on("submit", ".editor-form", function (e) {
                e.preventDefault();
                e.stopPropagation(); // for a case when table is within another form (model creation form for example)
                
                var filter_elem_selector = "." + table_class + " .filters-form";
                var filters_arr = $(filter_elem_selector).serializeArray();
                
                var post_data = [];
                $.merge(post_data, filters_arr);

                var editor_form_arr = $(this).serializeArray();
                $.merge(post_data, editor_form_arr);

                var pagination_elem_selector = "." + table_class + " .pagination";
                var pagination = $(pagination_elem_selector).data("params") || "";

                OLOG.preloader.show();

                $.ajax({
                    url: query_url,
                    type: "post",
                    data: post_data
                }).success(function (received_html) {
                    OLOG.preloader.hide();
                    var $box = $("<div>", {html: received_html});

                    var table_elem_selector = "." + table_class + " .table";
                    $(table_elem_selector).html($box.find(table_elem_selector).html());

                    var pagination_elem_selector = "." + table_class + " .pagination";
                    $(pagination_elem_selector).html($box.find(pagination_elem_selector).html());

                    CRUD.Table.clickTableRow(table_class);
                }).fail(function () {
                    OLOG.preloader.hide();
                });

            });
            
        </script>
        ';


    DemoLayoutTemplate::render($html, 'Термы', self::breadcrumbsArr());
    }
    
    static public function breadcrumbsArr(){
        return array_merge(DemoMainPageAction::breadcrumbsArr(), [BT::a(self::getUrl(), 'Terms')]);
    } 

}