<?php

namespace m2i\project\controllers;


use m2i\framework\FormManager;
use m2i\framework\Router;

class HomeController extends AbstractController
{

  public function indexAction()
  {

    $this->render("home/index", [ ]);

  }

  public function selectAction()
  {

    if (FormManager::isSubmitted()) {
      $dataset = $_POST["dataset"];
      if ($dataset != $_SESSION["dataset"]["id"]) {
        $_SESSION["dataset"] = [
          "id" => $dataset,
          "name" => DATASETS[$dataset]
        ];
      }
      Router::redirectTo(["home", "index"]);
      return;
    }

    $this->render("home/select",
      [
        "title" => "Catalogues"
      ]);

  }
}