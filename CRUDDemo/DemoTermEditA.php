<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace CRUDDemo;

use OLOG\CRUD\CForm;
use OLOG\CRUD\FGroupHidden;
use OLOG\CRUD\FRow;
use OLOG\CRUD\FGroup;
use OLOG\CRUD\FWInput;
use OLOG\CRUD\FWOptions;
use OLOG\CRUD\FWRadios;
use OLOG\CRUD\FWReferenceAjax;
use OLOG\CRUD\TCol;
use OLOG\CRUD\TFEqualHidden;
use OLOG\CRUD\TWDelete;
use OLOG\CRUD\TWTextWithLink;
use OLOG\CRUD\TWWeight;
use OLOG\MaskActionInterface;

class DemoTermEditA
    extends CRUDDemoABase
    implements MaskActionInterface
{
    protected $term_id;

    public function __construct($term_id)
    {
        $this->term_id = $term_id;
    }

    static public function mask()
    {
        return '/admin/term/(\d+)';
    }

    public function url()
    {
        return '/admin/term/' . $this->term_id;
    }

    public function action()
    {
        $term_id = $this->term_id;
        $html = '';

        $term_obj = DemoTerm::factory($term_id);

        $html .= CForm::html(
            $term_obj,
            [
                new FGroup(
                    'Title',
                    new FWInput('title', false, true),
                    'Comment string'
                ),
                new FGroup(
                    'weight',
                    new FWInput('weight', false, true)
                ),
                new FRow(
                    'Chooser',
                    new FWRadios('chooser', [
                        1 => 'one',
                        2 => 'two'
                    ], true, true)
                ),
	            new FRow(
		            'Gender',
		            new FWRadios('gender', [
			            1 => 'male',
			            2 => 'female'
		            ], true)
	            ),
                new FRow(
                    'Options',
                    new FWOptions('options', [
                        1 => 'one',
                        2 => 'two'
                    ], false, true)
                ),
                new FRow(
                    'Vocabulary',
                    new FWOptions('vocabulary_id', DemoTerm::VOCABULARIES_ARR, false, true)
                ),
                new FRow(
                    'Parent id',
                    new FWReferenceAjax(
                        'parent_id',
                        DemoTerm::class,
                        function (DemoTerm $demoterm){
                            return $demoterm->getId() . ': ' . $demoterm->getTitle();
                        },
                        (new DemoAjaxTermsListAction())->url(),
                        (new DemoTermEditA(FWReferenceAjax::REFERENCED_ID_PLACEHOLDER))->url()

                    )
                )
            ]
        );

        $html .= '<h2>Child terms</h2>';

        $new_term_obj = new DemoTerm();
        $new_term_obj->setParentId($term_id);

        $html .= \OLOG\CRUD\CTable::html(
            DemoTerm::class,
            \OLOG\CRUD\CForm::html(
                $new_term_obj,
                [
                    new FRow(
                        'Title',
                        new FWInput('title')
                    ),
                    new FGroupHidden(
                        new FWInput('parent_id')
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
                new TCol('', new TWWeight(['parent_id' => $term_id])),
                new TCol('', new TWDelete())
            ],
            [
                new TFEqualHidden('parent_id', $term_id)
            ],
            'weight'

        );

        $this->renderInLayout($html);
    }

}
