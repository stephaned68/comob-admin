<?php


namespace m2i\framework;


class Router
{

  /**
   * @var string
   */
  private $route;

  /**
   * @var string
   */
  private $controllerName = "HomeController";

  /**
   * @var string
   */
  private $actionName = "indexAction";

  /**
   * @var array
   */
  private $actionParameters = [];

  /**
   * Router constructor.
   * @param string $route
   */
  public function __construct($route)
  {
    $this->route = $route;

    $urlParts = explode("/", $route);

    if (count($urlParts) > 0 && !empty(trim($urlParts[0]))) {
      $this->controllerName = Tools::pascalize(array_shift($urlParts)) . "Controller";
    }

    if (count($urlParts) > 0 && !empty(trim($urlParts[0]))) {
      $this->actionName = Tools::camelize(array_shift($urlParts)) . "Action";
    }

    if (count($urlParts) > 0 && !empty(trim($urlParts[0]))) {
      array_map(function ($item) {
        return urldecode($item);
      }, $urlParts);
      $this->actionParameters = $urlParts;
    }
  }

  /**
   * @return string
   */
  public function getRoute()
  {
    return $this->route;
  }

  /**
   * @return string
   */
  public function getControllerName()
  {
    return $this->controllerName;
  }

  /**
   * @return string
   */
  public function getActionName()
  {
    return $this->actionName;
  }

  /**
   * @return array
   */
  public function getActionParameters()
  {
    return $this->actionParameters;
  }

  /**
   * @param array $args
   * @param array $query
   * @return string
   */
  public static function route($args = [], $query = [])
  {
    $url = "index.php?route=";
    if (count($args) > 0) {
      foreach ($args as $argK => $argV) {
        $args[$argK] = urlencode(trim($argV));
      }
      $url .= implode("/", $args);
    }
    if (count($query) > 0) {
      foreach ($query as $queryK => $queryV) {
        $query[$queryK] = urlencode(trim($queryV));
      }
      $url .= "?" . implode("&", $query);
    }
    return $url;
  }

  public static function redirectTo($args = [])
  {
    header("Location: " . self::route($args));
  }

}