<?php

namespace CRUDDemo;

use OLOG\BT\BT;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetOptions;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilter;
use OLOG\CRUD\CRUDTableWidgetDelete;
use OLOG\CRUD\CRUDTableWidgetText;
use OLOG\CRUD\CRUDTableWidgetTextWithLink;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDFormWidgetTextarea;

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

        $html .= \OLOG\CRUD\CRUDTable::html(
            \CRUDDemo\DemoTerm::class,
            CRUDForm::html(
                new DemoTerm,
                [
                    new CRUDFormRow(
                        'Title',
                        new CRUDFormWidgetInput('title', false, true)
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
                    'Parent',
                    new CRUDTableWidgetText(
                        '{' . DemoTerm::class . '.{this->parent_id}->title}'
                    )
                ),
                new CRUDTableColumn(
                    'Delete',
                    new CRUDTableWidgetDelete()
                )
            ],
            [
                new CRUDTableFilter('parent_id', CRUDTableFilter::FILTER_IS_NULL),
                new CRUDTableFilter('vocabulary_id', CRUDTableFilter::FILTER_EQUAL, DemoTerm::VOCABULARY_MAIN, new CRUDFormWidgetOptions('vocabulary_id', DemoTerm::VOCABULARIES_ARR)),
                new CRUDTableFilter('title', CRUDTableFilter::FILTER_LIKE, '')
            ],
            null,
            '8726438755234',
            CRUDTable::FILTERS_POSITION_TOP
        );

        DemoLayoutTemplate::render($html, 'Термы', self::breadcrumbsArr());
    }
    
    static public function breadcrumbsArr(){
        return array_merge(DemoMainPageAction::breadcrumbsArr(), [BT::a(self::getUrl(), 'Terms')]);
    } 

}