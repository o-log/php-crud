<?php

namespace CRUDDemo;

class MainPageController
{
    static public function mainPageAction($_mode){
        if ($_mode == \OLOG\Router::GET_URL) return '/';
        if ($_mode == \OLOG\Router::GET_METHOD) return __METHOD__;

        $html = '';
        $html .= '<div><a href="' . NodeCrudController::nodesListAction(\OLOG\Router::GET_URL) . '">NODES</a></div>';
        $html .= '<div><a href="' . TermCrudController::termsListAction(\OLOG\Router::GET_URL) . '">TERMS</a></div>';

        LayoutTemplate::render($html);
    }
}