<?php

namespace CRUDDemo;

use OLOG\BT\BT;
use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetAceTextarea;
use OLOG\CRUD\CRUDFormWidgetDate;
use OLOG\CRUD\CRUDFormWidgetDateTime;
use OLOG\CRUD\CRUDFormWidgetRadios;
use OLOG\CRUD\CRUDFormWidgetOptions;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilter;
use OLOG\CRUD\CRUDTableFilterEqual;
use OLOG\CRUD\CRUDTableFilterLike;
use OLOG\CRUD\CRUDTableWidgetDelete;
use OLOG\CRUD\CRUDTableWidgetText;
use OLOG\CRUD\CRUDTableWidgetTextWithLink;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDFormWidgetTextarea;
use OLOG\CRUD\CRUDTableWidgetWeight;

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
                    'Parent',
                    new CRUDTableWidgetText(
                        '{' . DemoTerm::class . '.{this->parent_id}->title}'
                    )
                ),
                new CRUDTableColumn(
                    'weight',
                    new CRUDTableWidgetWeight(
                        [
                            'parent_id' => null
                        ]
                    )
                ),
                new CRUDTableColumn(
                    'Delete',
                    new CRUDTableWidgetDelete()
                )
            ],
            [
                new CRUDTableFilter('parent_id', CRUDTableFilter::FILTER_IS_NULL),
                //new CRUDTableFilter('vocabulary_id', CRUDTableFilter::FILTER_EQUAL, DemoTerm::VOCABULARY_MAIN, new CRUDFormWidgetOptions('vocabulary_id', DemoTerm::VOCABULARIES_ARR)),
                new CRUDTableFilterEqual('34785ty8y45t8', 'Словарь', 'vocabulary_id', new CRUDFormWidgetOptions('vocabulary_id', DemoTerm::VOCABULARIES_ARR, true)),
                //new CRUDTableFilter('title', CRUDTableFilter::FILTER_LIKE, '')
                new CRUDTableFilterLike('3748t7t45gdfg', 'Название содержит', 'title')
            ],
            'weight',
            '8726438755234',
            CRUDTable::FILTERS_POSITION_TOP
        );

        DemoLayoutTemplate::render($html, 'Термы', self::breadcrumbsArr());
    }
    
    static public function breadcrumbsArr(){
        return array_merge(DemoMainPageAction::breadcrumbsArr(), [BT::a(self::getUrl(), 'Terms')]);
    } 

}