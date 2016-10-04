<?php

namespace CRUDDemo;

use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilterEqualInvisible;
use OLOG\CRUD\CRUDTableFilterLike;
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
                new CRUDTableFilterLike('38tiuwgerf', 'Название', 'title')
            ],
            '',
            25683745
        );

        echo $html;
    }

}