<?php

require_once '../vendor/autoload.php';

use \OLOG\Router;

\OLOG\ConfWrapper::assignConfig(\CRUDDemo\Config::get());

Router::matchClass(\CRUDDemo\MainPageAction::class);

Router::matchClass(\CRUDDemo\NodesListAction::class, 0);
Router::matchClass(\CRUDDemo\NodeEditAction::class, 0);
Router::matchClass(\CRUDDemo\NodeTermsAction::class, 0);

Router::matchClass(\CRUDDemo\TermsListAction::class, 0);
Router::matchClass(\CRUDDemo\TermEditAction::class, 0);