<?php

namespace CRUDDemo;

use OLOG\CRUD\CRUDEditorForm;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDList;
use OLOG\CRUD\CRUDWidgetReference;
use OLOG\CRUD\CRUDWidgetTextarea;

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

        ob_start();
        CRUDList::render(
            TermToNode::class,
            CRUDEditorForm::html(
                TermToNode::class,
                '',
                [
                    CRUDFormRow::html(
                        CRUDWidgetTextarea::html('node_id', $node_id),
                        'Node id'
                    ),
                    CRUDFormRow::html(
                        CRUDWidgetReference::html('term_id', '', Term::class, 'title'),
                        'Term id'
                    )
                ]
            ),
            [
                [
                    'COLUMN_TITLE' => 'node',
                    'WIDGET' => [
                        'WIDGET_TYPE' => 'TEXT',
                        'TEXT' => '{\CRUDDemo\Node.{this->node_id}->title}'
                    ]
                ],
                [
                    'COLUMN_TITLE' => 'term',
                    'WIDGET' => [
                        'WIDGET_TYPE' => 'TEXT',
                        'TEXT' => '{\CRUDDemo\Term.{this->term_id}->title}'
                    ]
                ],
                [
                    'COLUMN_TITLE' => 'term',
                    'WIDGET' => [
                        'WIDGET_TYPE' => 'DELETE',
                        'TEXT' => 'X'
                    ]
                ]
            ],
            ['node_id' => $node_id]
        );
        $html .= ob_get_clean();

        LayoutTemplate::render($html, 'Node ' . $node_id, NodeEditAction::breadcrumbsArr($node_id));
    }
}