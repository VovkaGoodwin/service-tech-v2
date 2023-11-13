<?php
define('ROOT', dirname(__DIR__));

const STATIC_FILES = ROOT . "/Static";
const ROUTES = ROOT . "/routes";
const CACHE = ROOT . "/tmp";

define('READ_COMM', env('READ_COMMUNITY'));
define('SET_COMM', env('SET_COMMUNITY'));