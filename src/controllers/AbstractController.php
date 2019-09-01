<?php


namespace m2i\project\controllers;

use m2i\framework\View;

abstract class AbstractController
{
  /**
   * @var View
   */
  private $view;

  /**
   * @var array
   */
  private $queryParams = [];

  /**
   * @param $layout
   * @return AbstractController
   */
  public function setView($layout): AbstractController
  {
    $this->view = new View($layout);
    return $this;
  }

  /**
   * @return array
   */
  public function getQueryParams(): array
  {
    return $this->queryParams;
  }

  /**
   * @param $name
   * @return mixed|null
   */
  public function getQueryParam($name)
  {
    return $this->queryParams[$name] ?? null;
  }

  /**
   * @param array $queryParams
   * @return AbstractController
   */
  public function setQueryParams(array $queryParams): AbstractController
  {
    $this->queryParams = $queryParams;
    return $this;
  }

  protected function render($template, $data = [])
  {
    echo $this->view->render($template, $data);
  }

}