<?php

use App\Facades\App;

App::bind('config.authorization', require 'config/authorization.php');
