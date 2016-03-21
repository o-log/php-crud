<?php

require_once '../vendor/autoload.php';

use \OLOG\Router;

\OLOG\ConfWrapper::assignConfig(\CRUDDemo\Config::get());

Router::matchClass(\CRUDDemo\DemoMainPageAction::class);

Router::matchClass(\CRUDDemo\DemoNodesListAction::class, 0);
Router::matchClass(\CRUDDemo\DemoNodeEditAction::class, 0);
Router::matchClass(\CRUDDemo\DemoNodeTermsAction::class, 0);

Router::matchClass(\CRUDDemo\DemoTermsListAction::class, 0);
Router::matchClass(\CRUDDemo\DemoTermEditAction::class, 0);