<?php

namespace CRUDDemo;

use OLOG\BT;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableWidgetDelete;
use OLOG\CRUD\CRUDTableWidgetText;
use OLOG\CRUD\CRUDTableWidgetTextWithLink;
use OLOG\CRUD\CRUDWidgetInput;

class NodesListAction
{
    static public function getUrl()
    {
        return '/nodes';
    }

    static public function action()
    {
        \OLOG\Exits::exit403If(!Auth::currentUserHasAnyOfPermissions([1]));

        $html = '';

        $html .= \OLOG\CRUD\CRUDEditorForm::html(
            new Node(),
            [
                new CRUDFormRow(
                    'Title',
                    new CRUDWidgetInput('title')
                )
            ]
        );

        $html .= CRUDTable::html(
            Node::class,
            [
                new CRUDTableColumn(
                    'Edit',
                    new CRUDTableWidgetText('{this->title}')
                ),
                new CRUDTableColumn(
                    'Edit',
                    new CRUDTableWidgetTextWithLink(
                        '{\CRUDDemo\Node.{this->id}->title}',
                        NodeEditAction::getUrl('{this->id}')
                    )
                ),
                new CRUDTableColumn(
                    'Edit',
                    new CRUDTableWidgetDelete()
                ),
            ]
        );

        LayoutTemplate::render($html, 'Nodes', self::getBreadcrumbsArr());
    }

    static public function getBreadcrumbsArr()
    {
        return array_merge(MainPageAction::breadcrumbsArr(), [BT::a(self::getUrl(), 'Nodes')]);
    }
}