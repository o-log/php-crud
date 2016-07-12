<?php

namespace CRUDDemo;

use OLOG\BT\BT;

class DemoMainPageAction
{
    static public function getUrl(){
        return '/';
    }
    
    public function action(){
        $html = '';
        $html .= '<div>';
        $html .= '<a class="btn btn-default" href="' . DemoNodesListAction::getUrl() . '">NODES</a> ';
        $html .= '<a class="btn btn-default" href="' . DemoTermsListAction::getUrl() . '">TERMS</a>';
        $html .= '</div>';

        DemoLayoutTemplate::render($html, 'Main page', self::breadcrumbsArr());
    }
    
    static public function breadcrumbsArr(){
        return [BT::a(self::getUrl(), 'Main')];
    }
}