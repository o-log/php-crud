<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace CRUDDemo;

use OLOG\ActionInterface;
use OLOG\CRUD\CForm;
use OLOG\CRUD\FRow;
use OLOG\CRUD\FWRadios;
use OLOG\CRUD\FWOptions;
use OLOG\CRUD\TCol;
use OLOG\CRUD\TFEqualOptionsInline;
use OLOG\CRUD\TFLikeInline;
use OLOG\CRUD\TWDelete;
use OLOG\CRUD\TWText;
use OLOG\CRUD\TWTextWithLink;
use OLOG\CRUD\FWInput;
use OLOG\CRUD\TWWeight;

class DemoTermsTreeAction
    extends CRUDDemoABase
    implements ActionInterface
{
    public function url()
    {
        return '/termstree';
    }

    public function action()
    {
        $html = '';

        $html .= \OLOG\CRUD\CTree::html(
            DemoTerm::class,
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
//                        '{' . DemoTerm::class . '.{this->parent_id}->title}'
                        function(DemoTerm $term){
                            return $term->parent() ? $term->parent()->title : 'X';
                        }
                    )
                ),
                new TCol(
                    'Edit',
                    new TWTextWithLink(
                        'title',
                        function (DemoTerm $term){
                            return (new DemoTermEditAction($term->getId()))->url();
                        }
                    )
                ),
                new TCol(
                    '',
                    new TWWeight(
                        [
                            'parent_id' => DemoTerm::_PARENT_ID
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
                new TFEqualOptionsInline(
                    '34785ty8y45t8',
                    'Словарь',
                    'vocabulary_id',
                    DemoTerm::VOCABULARIES_ARR,
                    false,
                    '',
                    false
                ),
                //new CRUDTableFilter('title', CRUDTableFilter::FILTER_LIKE, '')
                new TFLikeInline('3748t7t45gdfg', 'Название содержит', 'title')
            ],
            1
        );

        $this->renderInLayout($html);
    }

}
