<?php
session_start();

use framework\Router;
use framework\Dispatcher;
use framework\Tools;

define("ROOT_PATH", dirname(__DIR__));
const MODELS_PATH = ROOT_PATH . "/src/models";
const VIEWS_PATH = ROOT_PATH . "/src/views";
const CONTROLLERS_PATH = ROOT_PATH . "/src/controllers";
const PUBLIC_PATH = ROOT_PATH . "/public";

const DBHOST = "localhost";
const DBUSER = "root";
const DBPASS = "";
const DBNAME = "comob-db";

const DSN = "mysql:host=" . DBHOST . ";dbname=" . DBNAME . ";charset=UTF8";

const DATASETS = [
  "cof" => [
    "name" => "Fantasy"
  ],
  "cof2" => [
    "name" => "Fantasy 2E"
  ],
  "cota" => [
    "name" => "Terres d'Arran"
  ],
  "coct" => [
    "name" => "Cthulhu"
  ],
  "coc" => [
    "name" => "Contemporain"
  ],
  "cocy" => [
    "name" => "Cyberpunk"
  ],
  "cog" => [
    "name" => "Galactique"
  ]
];

const PAGINATION_AT = 12;

require ROOT_PATH . "/vendor/autoload.php";

if(!isset($_SESSION["dataset"])) {
  $ds = array_key_first(DATASETS);
  $_SESSION["dataset"] = [
    "id" => $ds,
    "name" => DATASETS[$ds]["name"]
  ];
}

Tools::setTheme();

$route = filter_input(INPUT_GET, "route", FILTER_SANITIZE_URL);
if ($route == "") {
  $route = $_SERVER["PATH_INFO"] ?? "";
  $route = substr($route,1);
  Router::$prefix = "/";
}

$router = new Router($route);

$dispatcher = new Dispatcher($router, "app\\controllers\\");
$dispatcher->run();

