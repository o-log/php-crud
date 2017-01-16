<?php

namespace CRUDDemo;

use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilterEqualInvisible;
use OLOG\CRUD\CRUDTableFilterEqualOptionsInline;
use OLOG\CRUD\CRUDTableFilterLike;
use OLOG\CRUD\CRUDTableFilterLikeInline;
use OLOG\CRUD\CRUDTableWidgetReferenceSelect;
use OLOG\CRUD\CRUDTableWidgetText;

class DemoAjaxTermsListAction
{
    static public function getUrl()
    {
        return '/ajax_terms';
    }

    public function action()
    {
        \OLOG\Exits::exit403If(!Auth::currentUserHasAnyOfPermissions([1]));

        $html = '';

        $html .= \OLOG\CRUD\CRUDTable::html(
            \CRUDDemo\DemoTerm::class,
            '',
            [
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetReferenceSelect('title')
                ),
                new CRUDTableColumn(
                    'Edit',
                    new CRUDTableWidgetText(
                        '{this->title}'
                    )
                ),
                new CRUDTableColumn(
                    'Parent',
                    new CRUDTableWidgetText(
                        '{' . DemoTerm::class . '.{this->parent_id}->title}'
                    )
                )
            ],
            [
                new CRUDTableFilterEqualInvisible('parent_id', null),
                new CRUDTableFilterEqualOptionsInline('34785ty8y45t8', 'Словарь', 'vocabulary_id', DemoTerm::VOCABULARIES_ARR, false, null, true),
                new CRUDTableFilterEqualOptionsInline('345634g3tg534', '', 'gender', DemoTerm::GENDER_ARR, false, null, true, 'М. и Ж.'),
                //new CRUDTableFilter('title', CRUDTableFilter::FILTER_LIKE, '')
                new CRUDTableFilterLikeInline('3748t7t45gdfg', '', 'title', 'Название содержит')
            ],
            '',
            25683745,
            CRUDTable::FILTERS_POSITION_INLINE
        );

        echo $html;
    }

}