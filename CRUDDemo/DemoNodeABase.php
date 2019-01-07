<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace CRUDDemo;

use OLOG\BT\BT4;
use OLOG\URL;

class DemoNodeABase
    extends CRUDDemoABase
{
    protected $node_id;

    public function topActionObj(){
        return new DemoNodesA();
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
        return BT4::tabsHtml(
            [
                BT4::tabHtml(
                    'Edit',
                    DemoNodeEditAction::mask(),
                    (new DemoNodeEditAction($node_id))->url(),
                    URL::path()
                ),
                BT4::tabHtml(
                    'Terms',
                    DemoNodeTermsAction::mask(),
                    (new DemoNodeTermsAction($node_id))->url(),
                    URL::path()
                )
            ]
        );
    }

}
