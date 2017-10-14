<?php

require_once 'vendor/autoload.php';

\CRUDDemo\CRUDDemoConfig::init();

\OLOG\DB\MigrateCLI::run();