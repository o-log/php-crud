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
use OLOG\CRUD\CRUDTableWidgetOptions;
use OLOG\CRUD\CRUDTableWidgetText;
use OLOG\CRUD\CRUDTableWidgetTextWithLink;
use OLOG\CRUD\CRUDFormWidgetInput;
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
                    new CRUDTableWidgetWeight(
                        [
                            'parent_id' => null
                        ]
                    )
                ),
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetDelete()
                )
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
            '8726438755234',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        DemoLayoutTemplate::render($html, 'Термы', self::breadcrumbsArr());
    }
    
    static public function breadcrumbsArr(){
        return array_merge(DemoMainPageAction::breadcrumbsArr(), [BT::a(self::getUrl(), 'Terms')]);
    } 

}