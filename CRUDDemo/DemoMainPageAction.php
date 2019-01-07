<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace CRUDDemo;

use OLOG\ActionInterface;
use OLOG\Layouts\PageTitleInterface;

class DemoMainPageAction
    extends CRUDDemoABase
    implements ActionInterface, PageTitleInterface
{
    public function pageTitle()
    {
        return 'MAIN';
    }

    public function url(){
        return '/';
    }

    public function action(){
        $html = '';
        $html .= '<div>';
        $html .= '<a class="btn btn-secondary" href="' . (new DemoNodesA())->url() . '">NODES</a> ';
        $html .= '<a class="btn btn-secondary" href="' . (new DemoTermsA())->url() . '">TERMS</a> ';
        $html .= '<a class="btn btn-secondary" href="' . (new DemoTermsTreeAction())->url() . '">TERMS TREE</a>';
        $html .= '</div>';

        $this->renderInLayout($html);
    }
}
