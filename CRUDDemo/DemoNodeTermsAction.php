<?php

namespace CRUDDemo;

use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormInvisibleRow;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilter;
use OLOG\CRUD\CRUDTableWidgetDelete;
use OLOG\CRUD\CRUDTableWidgetText;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDFormWidgetReference;
use OLOG\CRUD\CRUDFormWidgetTextarea;

class DemoNodeTermsAction
{
    static public function getUrl($node_id = '(\d+)')
    {
        return '/node/' . $node_id . '/terms';
    }

    public function action($node_id)
    {
        \OLOG\Exits::exit403If(!Auth::currentUserHasAnyOfPermissions([1]));

        $html = DemoNodeEditAction::tabsHtml($node_id);
        $html .= '<div>&nbsp;</div>';

        $new_term_to_node = new DemoTermToNode();
        $new_term_to_node->setNodeId($node_id);

        $html .= CRUDTable::html(
            DemoTermToNode::class,
            CRUDForm::html(
                $new_term_to_node,
                [
                    new CRUDFormInvisibleRow(
                        new CRUDFormWidgetInput('node_id')
                    ),
                    new CRUDFormRow(
                        'Term id',
                        new CRUDFormWidgetReference('term_id', DemoTerm::class, 'title'),
                        'Рубрика, с которой должен быть связан материал'
                    )
                ]
            ),
            [
                new CRUDTableColumn('Term', new CRUDTableWidgetText('{' . DemoTerm::class . '.{this->term_id}->title}')),
                new CRUDTableColumn('Delete', new CRUDTableWidgetDelete())
            ],
            [new CRUDTableFilter('node_id', CRUDTableFilter::FILTER_EQUAL,  $node_id)]
        );

        DemoLayoutTemplate::render($html, 'Node ' . $node_id, DemoNodeEditAction::breadcrumbsArr($node_id));
    }
}