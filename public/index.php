<?php

require_once '../vendor/autoload.php';

\OLOG\ConfWrapper::assignConfig(\CRUDDemo\Config::get());

\OLOG\Router::match3(\CRUDDemo\MainPageController::mainPageAction(\OLOG\Router::GET_METHOD));

\OLOG\CRUD\CRUDController::routing();