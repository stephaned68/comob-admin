<?php


namespace framework;


class Router
{

  /**
   * @var string
   */
  private string $route;

  /**
   * @var string
   */
  private string $controllerName = "HomeController";

  /**
   * @var string
   */
  private string $actionName = "indexAction";

  /**
   * @var string
   */
  private string $getActionName = "getIndex";

  /**
   * @var string
   */
  private string $postActionName = "postIndex";

  /**
   * @var array
   */
  private $actionParameters = [];

  /**
   * @var array
   */
  private array $queryParams = [];

  /**
   * Router constructor.
   * @param string $route
   */
  public function __construct(string $route)
  {
    $this->route = $route;

    $urlParts = explode("/", $route);

    if (count($urlParts) > 0 && !empty(trim($urlParts[0]))) {
      $this->controllerName = Tools::pascalize(array_shift($urlParts)) . "Controller";
    }

    if (count($urlParts) > 0 && !empty(trim($urlParts[0]))) {
      $action = array_shift($urlParts);
      $this->actionName = Tools::camelize($action) . "Action";
      $this->getActionName = "get" . Tools::pascalize($action);
      $this->postActionName = "post" . Tools::pascalize($action);
    }

    if (count($urlParts) > 0 && !empty(trim($urlParts[0]))) {
      array_map(function ($item) {
        return urldecode($item);
      }, $urlParts);
      $this->actionParameters = $urlParts;
    }

    $queryParams = $_GET;
    if (isset($queryParams["route"])) {
      array_shift($queryParams);
    }
    $this->queryParams = $queryParams;

  }

  /**
   * @return string
   */
  public function getRoute(): string
  {
    return $this->route;
  }

  /**
   * @return string
   */
  public function getControllerName(): string
  {
    return $this->controllerName;
  }

  /**
   * @return string
   */
  public function getActionName(): string
  {
    return $this->actionName;
  }

  /**
   * @return string
   */
  public function getGetActionName(): string
  {
    return $this->getActionName;
  }

  /**
   * @return string
   */
  public function getPostActionName(): string
  {
    return $this->postActionName;
  }

  /**
   * @return array
   */
  public function getActionParameters(): array
  {
    return $this->actionParameters;
  }

  /**
   * @return array
   */
  public function getQueryParams(): array
  {
    return $this->queryParams;
  }

  /**
   * @var string
   */
  public static string $prefix = "index.php?route=";

  /**
   * @param array $args
   * @param array $query
   * @return string
   */
  public static function route(array $args = [], array $query = []): string
  {
    $url = self::$prefix; // "/" or // "index.php?route="
    if (count($args) > 0) {
      foreach ($args as $argK => $argV) {
        $args[$argK] = urlencode(trim($argV ?? ""));
      }
      $url .= implode("/", $args);
    }
    if (count($query) > 0) {
      $queryArgs = [];
      foreach ($query as $queryK => $queryV) {
        $queryArgs[] = $queryK . "=" . urlencode(trim($queryV));
      }
      $url .= "&" . implode("&", $queryArgs);
    }
    return $url;
  }

  public static function redirectTo($args = [])
  {
    header("Location: " . self::route($args));
  }

}