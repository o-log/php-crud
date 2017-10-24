<?php

namespace CRUDDemo;

use OLOG\ActionInterface;
use OLOG\BT\BT;
use OLOG\CRUD\CForm;
use OLOG\CRUD\FRow;
use OLOG\CRUD\FWRadios;
use OLOG\CRUD\FWOptions;
use OLOG\CRUD\CTable;
use OLOG\CRUD\TCol;
use OLOG\CRUD\TFEqualOptions;
use OLOG\CRUD\TFEqualOptionsInline;
use OLOG\CRUD\TFLike;
use OLOG\CRUD\TFLikeInline;
use OLOG\CRUD\TWDelete;
use OLOG\CRUD\TWText;
use OLOG\CRUD\TWTextWithLink;
use OLOG\CRUD\FWInput;
use OLOG\CRUD\TWWeight;
use OLOG\Layouts\AdminLayoutSelector;

class DemoTermsTreeAction
    implements ActionInterface
{
    public function url()
    {
        return '/termstree';
    }

    public function action()
    {
        \OLOG\Exits::exit403If(!CRUDDemoAuth::currentUserHasAnyOfPermissions([1]));

        $html = '';

        $html .= \OLOG\CRUD\CTree::html(
            \CRUDDemo\DemoTerm::class,
            CForm::html(
                new DemoTerm,
                [
                    new FRow(
                        'Title',
                        new FWInput('title', false, true)
                    ),
                    new FRow(
                        'Chooser',
                        new FWRadios('chooser', [
                            1 => 'one',
                            2 => 'two'
                        ], true, true)
                    ),
                    new FRow(
                        'Options',
                        new FWOptions('options', [
                            1 => 'one',
                            2 => 'two'
                        ], false, true)
                    )
                ]
            ),
            [
                new TCol(
                    'Parent',
                    new TWText(
                        '{' . DemoTerm::class . '.{this->parent_id}->title}'
                    )
                ),
                new TCol(
                    'Edit',
                    new TWTextWithLink(
                        '{this->title}',
                        (new DemoTermEditAction('{this->id}'))->url()
                    )
                ),
                new TCol(
                    '',
                    new TWWeight(
                        [
                            'parent_id' => '{this->parent_id}'
                        ]
                    )
                ),
                new TCol(
                    '',
                    new TWDelete()
                )
            ],
            'parent_id',
            'weight',
            '8726438755234',
            [
                //new CRUDTableFilter('parent_id', CRUDTableFilter::FILTER_IS_NULL),
                //new CRUDTableFilter('vocabulary_id', CRUDTableFilter::FILTER_EQUAL, DemoTerm::VOCABULARY_MAIN, new CRUDFormWidgetOptions('vocabulary_id', DemoTerm::VOCABULARIES_ARR)),
                new TFEqualOptionsInline('34785ty8y45t8', 'Словарь', 'vocabulary_id', DemoTerm::VOCABULARIES_ARR, true, DemoTerm::VOCABULARY_MAIN, false),
                //new CRUDTableFilter('title', CRUDTableFilter::FILTER_LIKE, '')
                new TFLikeInline('3748t7t45gdfg', 'Название содержит', 'title')
            ],
            1,
            CTable::FILTERS_POSITION_INLINE
        );

        AdminLayoutSelector::render($html, $this);
    }

}