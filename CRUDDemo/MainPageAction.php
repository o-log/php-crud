<?php

namespace CRUDDemo;

use OLOG\BT;

class MainPageAction
{
    static public function getUrl(){
        return '/';
    }
    
    static public function action(){
        $html = '';
        $html .= '<div><a href="' . NodesListAction::getUrl() . '">NODES</a></div>';
        $html .= '<div><a href="' . TermsListAction::getUrl() . '">TERMS</a></div>';

        LayoutTemplate::render($html, 'Main page', self::breadcrumbsArr());
    }
    
    static public function breadcrumbsArr(){
        return [BT::a(self::getUrl(), 'Main')];
    }
}