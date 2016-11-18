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

class DemoTermEditAction
{
    static public function getUrl($term_id = '(\d+)')
    {
        return '/admin/term/' . $term_id;
    }

    public function action($term_id)
    {
        \OLOG\Exits::exit403If(!Auth::currentUserHasAnyOfPermissions([1]));

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
                        DemoTermEditAction::getUrl('REFERENCED_ID')

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
                new CRUDTableColumn('Title', new CRUDTableWidgetTextWithLink('{this->title}', DemoTermEditAction::getUrl('{this->id}'))),
                new CRUDTableColumn('Weight', new CRUDTableWidgetWeight(['parent_id' => $term_id])),
                new CRUDTableColumn('Delete', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterEqualInvisible('parent_id', $term_id)
            ],
            'weight'

        );

        DemoLayoutTemplate::render($html, 'Term ' . $term_id, self::breadcrumbsArr($term_id));
    }

    static public function breadcrumbsArr($term_id)
    {
        return array_merge(DemoTermsListAction::breadcrumbsArr(), [BT::a(self::getUrl($term_id), 'Term ' . $term_id)]);
    }

}