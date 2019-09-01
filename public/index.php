<?php
session_start();

use m2i\framework\Router;
use m2i\framework\Dispatcher;

define("ROOT_PATH", dirname(__DIR__));
define("MODELS_PATH", dirname(__DIR__) . "/src/models");
define("VIEWS_PATH", dirname(__DIR__) . "/src/views");
define("CONTROLLERS_PATH", dirname(__DIR__) . "/src/controllers");
define("PUBLIC_PATH", dirname(__DIR__) . "/public");

define("DATABASE", "comobdb");

define("DSN", "mysql:host=localhost;dbname=" . DATABASE . ";charset=utf8mb4");

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

$route = filter_input(INPUT_GET, "route", FILTER_SANITIZE_URL);

$router = new Router($route);

$dispatcher = new Dispatcher($router, "m2i\\project\\controllers\\");
$dispatcher->run();

