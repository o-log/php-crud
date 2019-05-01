<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace CRUDDemo;

use OLOG\ActionInterface;
use OLOG\CRUD\CForm;
use OLOG\CRUD\FGroup;
use OLOG\CRUD\FWRadios;
use OLOG\CRUD\FWOptions;
use OLOG\CRUD\FWReferenceAjax;
use OLOG\CRUD\CTable;
use OLOG\CRUD\TCol;
use OLOG\CRUD\TFEqualInline;
use OLOG\CRUD\TFEqualOptionsInline;
use OLOG\CRUD\TFLikeInline;
use OLOG\CRUD\TWDelete;
use OLOG\CRUD\TWOptions;
use OLOG\CRUD\TWOptionsEditor;
use OLOG\CRUD\TWText;
use OLOG\CRUD\TWTextEditor;
use OLOG\CRUD\TWTextWithLink;
use OLOG\CRUD\FWInput;
use OLOG\CRUD\TWWeight;
use OLOG\Layouts\PageTitleInterface;
use OLOG\Layouts\TopActionObjInterface;

class DemoTermsA
    extends CRUDDemoABase
    implements ActionInterface, PageTitleInterface, TopActionObjInterface
{
    public function topActionObj()
    {
        return new DemoMainA();
    }

    public function pageTitle()
    {
        return 'Terms';
    }

    public function url()
    {
        return '/terms';
    }

    public function action()
    {
        $html = '';

        $table_id = '8726438755234';

        $html .= CTable::html(
            DemoTerm::class,
            CForm::html(
                new DemoTerm,
                [
                    new FGroup('Title', new FWInput('title', false, true)),
                    new FGroup('Chooser', new FWRadios('chooser', [1 => 'one', 2 => 'two'], true, true)),
                    new FGroup('Options', new FWOptions('options', [1 => 'one', 2 => 'two'], false, true)),
                    new FGroup('Parent id', new FWReferenceAjax(
                            'parent_id',
                            DemoTerm::class,
                            'title',
                            (new DemoAjaxTermsListAction())->url(),
                            (new DemoTermEditA(FWReferenceAjax::REFERENCED_ID_PLACEHOLDER))->url()

                        )
                    )

                ]
            ),
            [
                new TCol(
                    '',
                    new TWTextWithLink(
                        DemoTerm::_TITLE,
                        function(DemoTerm $term){
                            return (new DemoTermEditA($term->getId()))->url();
                        }
                    )
                ),
                new TCol(
                    '',
                    new TWTextEditor('title', 'title', $table_id)
                ),
	            new TCol(
		            '',
		            new TWOptions(
			            'vocabulary_id',
			            DemoTerm::VOCABULARIES_ARR
		            )
	            ),
	            new TCol(
		            '',
		            new TWOptionsEditor('vocabulary_id', DemoTerm::VOCABULARIES_ARR, $table_id)
	            ),
                new TCol(
                    '',
                    new TWText(
                        function (DemoTerm $term){
                            return $term->parent() ? $term->parent()->title : '-';
                        }
                    )
                ),
                new TCol('', new TWWeight(['parent_id' => null])),
                new TCol('', new TWDelete())
            ],
            [
                //new CRUDTableFilterEqualInvisible('parent_id', null),
                //new CRUDTableFilter('vocabulary_id', CRUDTableFilter::FILTER_EQUAL, DemoTerm::VOCABULARY_MAIN, new CRUDFormWidgetOptions('vocabulary_id', DemoTerm::VOCABULARIES_ARR)),
                new TFEqualOptionsInline('34785ty8y45t8', 'Словарь', 'vocabulary_id', DemoTerm::VOCABULARIES_ARR, false, null, true),
	            new TFEqualOptionsInline('345634g3tg534', '', 'gender', DemoTerm::GENDER_ARR, false, null, true, 'М. и Ж.'),
                //new CRUDTableFilter('title', CRUDTableFilter::FILTER_LIKE, '')
                new TFLikeInline('3748t7t45gdfg', '', 'title', 'Название содержит'),
                new TFEqualInline('345634g2v35234', '', 'title', 'Название')
            ],
            'weight',
            $table_id,
            'Terms',
            true
        );

        $this->renderInLayout($html);
    }

}
