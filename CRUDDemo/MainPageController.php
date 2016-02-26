<?php

namespace CRUDDemo;

class MainPageController
{
    static public function mainPageAction($_mode){
        if ($_mode == \OLOG\Router::GET_URL) return '/';
        if ($_mode == \OLOG\Router::GET_METHOD) return __METHOD__;

        echo '<a href="' . \OLOG\CRUD\CRUDController::listAction(\OLOG\Router::GET_URL, 'node') . '">NODES</a>';
    }
}