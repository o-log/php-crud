<?php

namespace CRUDDemo;

use OLOG\CRUD\CRUDForm;
use OLOG\CRUD\CRUDFormInvisibleRow;
use OLOG\CRUD\CRUDFormRow;
use OLOG\CRUD\CRUDTable;
use OLOG\CRUD\CRUDTableColumn;
use OLOG\CRUD\CRUDTableFilterEqualInvisible;
use OLOG\CRUD\CRUDTableWidgetDelete;
use OLOG\CRUD\CRUDTableWidgetText;
use OLOG\CRUD\CRUDFormWidgetInput;
use OLOG\CRUD\CRUDFormWidgetReference;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\PageTitleInterface;
use OLOG\Layouts\TopActionObjInterface;
use OLOG\MaskActionInterface;

class DemoNodeTermsAction
    extends DemoNodeBase
    implements MaskActionInterface, PageTitleInterface, TopActionObjInterface
{
    static public function mask()
    {
        return '/node/(\d+)/terms';
    }

    public function url()
    {
        return '/node/' . $this->node_id . '/terms';
    }

    public function action()
    {
        \OLOG\Exits::exit403If(!CRUDDemoAuth::currentUserHasAnyOfPermissions([1]));

        $html = self::tabsHtml($this->node_id);
        $html .= '<div>&nbsp;</div>';

        $new_term_to_node = new DemoTermToNode();
        $new_term_to_node->setNodeId($this->node_id);

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
            [new CRUDTableFilterEqualInvisible('node_id', $this->node_id)]
        );

        AdminLayoutSelector::render($html, $this);
    }
}