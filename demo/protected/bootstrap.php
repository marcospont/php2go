<?php

require_once '../php2go/Php2Go.php';

Php2Go::createWebApplication(Config::fromFile('protected/config/main.php'));