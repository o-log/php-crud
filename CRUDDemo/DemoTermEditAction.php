<?php

namespace CRUDDemo;

use OLOG\BT\BT;
use OLOG\CRUD\CRUDFormInvisibleRow;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormVerticalRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDFormWidgetOptions;
use OLOG\CRUD\CRUDFormWidgetRadios;
use OLOG\CRUD\CRUDFormWidgetReferenceAjax;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilterEqualInvisible;
use OLOG\CRUD\CRUDTableWidgetDelete;
use OLOG\CRUD\CRUDTableWidgetTextWithLink;
use OLOG\CRUD\CRUDTableWidgetWeight;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\MaskActionInterface;

class DemoTermEditAction implements MaskActionInterface
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
        \OLOG\Exits::exit403If(!CRUDDemoAuth::currentUserHasAnyOfPermissions([1]));

        $term_id = $this->term_id;
        $html = '';

        $term_obj = DemoTerm::factory($term_id);

        $html .= \OLOG\CRUD\CRUDForm::html(
            $term_obj,
            [
                new CRUDFormVerticalRow(
                    'Title',
                    new CRUDFormWidgetInput('title', false, true),
                    'Comment string'
                ),
                new CRUDFormVerticalRow(
                    'weight',
                    new CRUDFormWidgetInput('weight', false, true)
                ),
                new CRUDFormRow(
                    'Chooser',
                    new CRUDFormWidgetRadios('chooser', [
                        1 => 'one',
                        2 => 'two'
                    ], true, true)
                ),
	            new CRUDFormRow(
		            'Gender',
		            new CRUDFormWidgetRadios('gender', [
			            1 => 'male',
			            2 => 'female'
		            ], true)
	            ),
                new CRUDFormRow(
                    'Options',
                    new CRUDFormWidgetOptions('options', [
                        1 => 'one',
                        2 => 'two'
                    ], false, true)
                ),
                new CRUDFormRow(
                    'Vocabulary',
                    new CRUDFormWidgetOptions('vocabulary_id', DemoTerm::VOCABULARIES_ARR, false, true)
                ),
                new CRUDFormRow(
                    'Parent id',
                    new CRUDFormWidgetReferenceAjax(
                        'parent_id',
                        DemoTerm::class,
                        'title',
                        DemoAjaxTermsListAction::getUrl(),
                        (new DemoTermEditAction(CRUDFormWidgetReferenceAjax::REFERENCED_ID_PLACEHOLDER))->url()

                    )
                )
            ]
        );

        $html .= '<h2>Child terms</h2>';

        $new_term_obj = new DemoTerm();
        $new_term_obj->setParentId($term_id);

        $html .= \OLOG\CRUD\CRUDTable::html(
            \CRUDDemo\DemoTerm::class,
            \OLOG\CRUD\CRUDForm::html(
                $new_term_obj,
                [
                    new CRUDFormRow(
                        'Title',
                        new CRUDFormWidgetInput('title')
                    ),
                    new CRUDFormInvisibleRow(
                        new CRUDFormWidgetInput('parent_id')
                    )
                ]
            ),
            [
                new CRUDTableColumn('', new CRUDTableWidgetTextWithLink('{this->title}', (new DemoTermEditAction('{this->id}'))->url())),
                new CRUDTableColumn('', new CRUDTableWidgetWeight(['parent_id' => $term_id])),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterEqualInvisible('parent_id', $term_id)
            ],
            'weight'

        );

        AdminLayoutSelector::render($html, $this);
    }

}