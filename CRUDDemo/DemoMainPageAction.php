<?php

namespace CRUDDemo;

use OLOG\BT;

class DemoMainPageAction
{
    static public function getUrl(){
        return '/';
    }
    
    static public function action(){
        $html = '';
        $html .= '<div><a href="' . DemoNodesListAction::getUrl() . '">NODES</a></div>';
        $html .= '<div><a href="' . DemoTermsListAction::getUrl() . '">TERMS</a></div>';

        DemoLayoutTemplate::render($html, 'Main page', self::breadcrumbsArr());
    }
    
    static public function breadcrumbsArr(){
        return [BT::a(self::getUrl(), 'Main')];
    }
}