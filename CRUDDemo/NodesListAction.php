<?php

namespace CRUDDemo;

use OLOG\CRUD\CRUDList;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDWidgetTextarea;

class NodesListAction
{
    static public function getUrl(){
        return '/nodes';
    }
    
    static public function action()
    {
        \OLOG\Exits::exit403If(!Auth::currentUserHasAnyOfPermissions([1]));

        ob_start();
        CRUDList::render(
            Node::class,
            \OLOG\CRUD\CRUDEditorForm::html(Node::class, null,
                [
                    CRUDFormRow::html(
                        CRUDWidgetTextarea::html('title'),
                        'Title'
                    ),
                    CRUDFormRow::html(
                        CRUDWidgetTextarea::html('state_code'),
                        'State code'
                    )
                ]
            ),
            [
                [
                    'COLUMN_TITLE' => 'Edit',
                    'WIDGET' => [
                        'WIDGET_TYPE' => 'TEXT',
                        'TEXT' => '{this->title}'
                    ]
                ],
                [
                    'COLUMN_TITLE' => 'Edit',
                    'WIDGET' => [
                        'WIDGET_TYPE' => \OLOG\CRUD\CRUDWidgets::WIDGET_TEXT_WITH_LINK,
                        'LINK_URL' => NodeEditAction::getUrl('{this->id}'),
                        'TEXT' => '{\CRUDDemo\Node.{this->id}->title}'
                    ]
                ]
            ]
        );
        $html = ob_get_clean();

        LayoutTemplate::render($html, 'Nodes', self::getBreadcrumbsArr());
    }

    static public function getBreadcrumbsArr(){
        return array_merge(MainPageAction::breadcrumbsArr(), [BT::a(self::getUrl(), 'Nodes')]);
    }
}