<?php

/** autoload for Composer's libraries */
require_once __DIR__ . "/vendor/autoload.php";
/** autoload for project's files */
require_once __DIR__ . "/project_autoloader.php";
/** autoload for states (that have different rules of autoloading and namespace handling) */
require_once __DIR__ . "/control/states_autoloader.php";

use OBCBot\init\Init;

$development_mode = "testing";
Init::initRequestProcessing($development_mode);

