<?php

namespace CRUDDemo;

use OLOG\ActionInterface;
use OLOG\Layouts\AdminLayoutSelector;
use OLOG\Layouts\PageTitleInterface;

class DemoMainPageAction implements ActionInterface, PageTitleInterface
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
        $html .= '<a class="btn btn-secondary" href="' . (new DemoNodesListAction())->url() . '">NODES</a> ';
        $html .= '<a class="btn btn-secondary" href="' . (new DemoTermsListAction())->url() . '">TERMS</a> ';
        $html .= '<a class="btn btn-secondary" href="' . (new DemoTermsTreeAction())->url() . '">TERMS TREE</a>';
        $html .= '</div>';

        AdminLayoutSelector::render($html, $this);
    }
}