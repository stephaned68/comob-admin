<?php
session_start();

use framework\Router;
use framework\Dispatcher;
use framework\Tools;

define("ROOT_PATH", dirname(__DIR__));
define("MODELS_PATH", dirname(__DIR__) . "/src/models");
define("VIEWS_PATH", dirname(__DIR__) . "/src/views");
define("CONTROLLERS_PATH", dirname(__DIR__) . "/src/controllers");
define("PUBLIC_PATH", dirname(__DIR__) . "/public");

define("DATABASE", "comobdb");

define("DSN", "mysql:host=localhost;dbname=" . DATABASE . ";charset=UTF8");

define ("DATASETS",
  [
    "cof" => [
      "name" => "Fantasy"
      ],
    "coc" => [
      "name" => "Contemporain"
    ],
    "cocy" => [
      "name" => "Cyberpunk"
    ],
    "cga" => [
      "name" => "Galactique"
    ]
  ]);

define("PAGINATION_AT", 12);

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

