<?php

namespace CRUDDemo;

use OLOG\BT\BT;
use OLOG\CRUD\CRUDFormInvisibleRow;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilter;
use OLOG\CRUD\CRUDTableWidgetDelete;
use OLOG\CRUD\CRUDTableWidgetText;
use OLOG\CRUD\CRUDFormWidgetReference;
use OLOG\CRUD\CRUDFormWidgetTextarea;

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
                new CRUDFormRow(
                    'Title',
                    new CRUDFormWidgetInput('title')
                ),
                new CRUDFormRow(
                    'Parent id',
                    new CRUDFormWidgetReference('parent_id', DemoTerm::class, 'title')
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
                        new CRUDFormWidgetReference('parent_id', DemoTerm::class, 'title')
                    )
                ]
            ),
            [
                new CRUDTableColumn('Title', new CRUDTableWidgetText('{this->title}')),
                new CRUDTableColumn('Delete', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilter('parent_id', CRUDTableFilter::FILTER_EQUAL, $term_id)
            ]
        );

        DemoLayoutTemplate::render($html, 'Term ' . $term_id, self::breadcrumbsArr($term_id));
    }

    static public function breadcrumbsArr($term_id)
    {
        return array_merge(DemoTermsListAction::breadcrumbsArr(), [BT::a(self::getUrl($term_id), 'Term ' . $term_id)]);
    }

}