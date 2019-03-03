<?php

require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\Facades\Router;
use App\Facades\Request;

Router::load('app/routes.php')->direct(Request::uri(), Request::method());
