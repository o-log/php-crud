<?php

require_once '../vendor/autoload.php';

use \OLOG\Router;

\OLOG\ConfWrapper::assignConfig(\CRUDDemo\Config::get());

\OLOG\Router::match3(\CRUDDemo\MainPageController::mainPageAction(\OLOG\Router::GET_METHOD));

Router::match3(\CRUDDemo\NodeCrudController::nodesListAction(Router::GET_METHOD), 0);
Router::match3(\CRUDDemo\NodeCrudController::nodeEditAction(Router::GET_METHOD), 0);
Router::match3(\CRUDDemo\NodeCrudController::nodeTermsAction(Router::GET_METHOD), 0);
