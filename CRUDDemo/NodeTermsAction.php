<?php

namespace CRUDDemo;

use OLOG\CRUD\CRUDEditorForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableWidgetDelete;
use OLOG\CRUD\CRUDTableWidgetText;
use OLOG\CRUD\CRUDWidgetInput;
use OLOG\CRUD\CRUDWidgetReference;
use OLOG\CRUD\CRUDEditorWidgetTextarea;

class NodeTermsAction
{
    static public function getUrl($node_id = '(\d+)')
    {
        return '/node/' . $node_id . '/terms';
    }

    static public function action($node_id)
    {
        \OLOG\Exits::exit403If(!Auth::currentUserHasAnyOfPermissions([1]));

        $html = NodeEditAction::tabsHtml($node_id);

        $new_term_to_node = new TermToNode();
        $new_term_to_node->setNodeId($node_id);

        $html .= CRUDEditorForm::html(
            $new_term_to_node,
            [
                new CRUDFormRow(
                    'Node id',
                    new CRUDWidgetInput('node_id')
                ),
                new CRUDFormRow(
                    'Term id',
                    new CRUDWidgetReference('term_id', Term::class, 'title')
                )
            ]
        );

        $html .= CRUDTable::html(
            TermToNode::class,
            [
                new CRUDTableColumn('Term', new CRUDTableWidgetText('{\CRUDDemo\Term.{this->term_id}->title}')),
                new CRUDTableColumn('Delete', new CRUDTableWidgetDelete())
            ],
            ['node_id' => $node_id]
        );

        LayoutTemplate::render($html, 'Node ' . $node_id, NodeEditAction::breadcrumbsArr($node_id));
    }
}