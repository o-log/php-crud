<?php

namespace CRUDDemo;

use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilter;
use OLOG\CRUD\CRUDTableWidgetReferenceSelect;
use OLOG\CRUD\CRUDTableWidgetText;
use OLOG\CRUD\CRUDTableWidgetTextWithLink;

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
                new CRUDTableFilter('parent_id', CRUDTableFilter::FILTER_IS_NULL),
                new CRUDTableFilter('title', CRUDTableFilter::FILTER_LIKE, '%'),
            ]
        );

        echo $html;
    }

}