<?php

namespace CRUDDemo;

use OLOG\CRUD\CForm;
use OLOG\CRUD\FGroupHidden;
use OLOG\CRUD\FRow;
use OLOG\CRUD\CTable;
use OLOG\CRUD\TCol;
use OLOG\CRUD\TFEqualHidden;
use OLOG\CRUD\TWDelete;
use OLOG\CRUD\TWText;
use OLOG\CRUD\FWInput;
use OLOG\CRUD\FWReference;
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

        $html .= CTable::html(
            DemoTermToNode::class,
            CForm::html(
                $new_term_to_node,
                [
                    new FGroupHidden(
                        new FWInput('node_id')
                    ),
                    new FRow(
                        'Term id',
                        new FWReference('term_id', DemoTerm::class, 'title'),
                        'Рубрика, с которой должен быть связан материал'
                    )
                ]
            ),
            [
                new TCol('Term', new TWText('{' . DemoTerm::class . '.{this->term_id}->title}')),
                new TCol('Delete', new TWDelete())
            ],
            [new TFEqualHidden('node_id', $this->node_id)]
        );

        AdminLayoutSelector::render($html, $this);
    }
}