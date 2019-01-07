<?php

require_once '../vendor/autoload.php';

use \OLOG\Router;

CRUDDemo\CrudDemoConfig::init();

Router::action(\CRUDDemo\DemoMainPageAction::class, 0);

Router::action(\CRUDDemo\DemoNodesA::class, 0);
Router::action(\CRUDDemo\DemoNodeEditAction::class, 0);
Router::action(\CRUDDemo\DemoNodeTermsAction::class, 0);

Router::action(\CRUDDemo\DemoTermsA::class, 0);
Router::action(\CRUDDemo\DemoTermsTreeAction::class, 0);
Router::action(\CRUDDemo\DemoTermEditAction::class, 0);

Router::action(\CRUDDemo\DemoAjaxTermsListAction::class, 0);
