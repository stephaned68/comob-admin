<?php


namespace m2i\project\controllers;

use m2i\framework\View;

abstract class AbstractController
{
  /**
   * @var View
   */
  private $view;


  public function setView($layout)
  {
    $this->view = new View($layout);
  }

  protected function render($template, $data = [])
  {
    echo $this->view->render($template, $data);
  }

}