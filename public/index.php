<?php

require_once '../vendor/autoload.php';

use \OLOG\Router;

\OLOG\ConfWrapper::assignConfig(\CRUDDemo\Config::get());

Router::matchAction(\CRUDDemo\DemoMainPageAction::class, 0);

Router::matchAction(\CRUDDemo\DemoNodesListAction::class, 0);
Router::matchAction(\CRUDDemo\DemoNodeEditAction::class, 0);
Router::matchAction(\CRUDDemo\DemoNodeTermsAction::class, 0);

Router::matchAction(\CRUDDemo\DemoTermsListAction::class, 0);
Router::matchAction(\CRUDDemo\DemoTermEditAction::class, 0);