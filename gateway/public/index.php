<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

/* application bootstrap */
$appli = require_once __DIR__ . '/../config/bootstrap.php';


$appli->run();
