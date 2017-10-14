<?php

require_once '../vendor/autoload.php';

use \OLOG\Router;

\CRUDDemo\CRUDDemoConfig::init();

Router::action(\CRUDDemo\DemoMainPageAction::class, 0);

Router::action(\CRUDDemo\DemoNodesListAction::class, 0);
Router::action(\CRUDDemo\DemoNodeEditAction::class, 0);
Router::action(\CRUDDemo\DemoNodeTermsAction::class, 0);

Router::action(\CRUDDemo\DemoTermsListAction::class, 0);
Router::action(\CRUDDemo\DemoTermsTreeAction::class, 0);
Router::action(\CRUDDemo\DemoTermEditAction::class, 0);

Router::action(\CRUDDemo\DemoAjaxTermsListAction::class, 0);