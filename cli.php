<?php

require_once 'vendor/autoload.php';

\OLOG\ConfWrapper::assignConfig(\CRUDDemo\Config::get());

\OLOG\Model\CLI\CLIMenu::run();
