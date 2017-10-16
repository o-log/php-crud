<?php

namespace CRUDDemo;

use OLOG\BT\BT;
use OLOG\URL;

class DemoNodeBase
{
    protected $node_id;

    public function topActionObj(){
        return new DemoNodesListAction();
    }

    public function pageTitle()
    {
        return 'Node ' . $this->node_id;
    }

    public function __construct($node_id)
    {
        $this->node_id = $node_id;
    }

    static public function tabsHtml($node_id)
    {
        return BT::tabsHtml(
            [
                BT::tabHtml(
                    'Edit',
                    DemoNodeEditAction::mask(),
                    (new DemoNodeEditAction($node_id))->url(),
                    URL::path()
                ),
                BT::tabHtml(
                    'Terms',
                    DemoNodeTermsAction::mask(),
                    (new DemoNodeTermsAction($node_id))->url(),
                    URL::path()
                )
            ]
        );
    }

}