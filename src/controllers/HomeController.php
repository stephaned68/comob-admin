<?php

namespace app\controllers;


use framework\FormManager;
use framework\Router;

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
          "name" => DATASETS[$dataset]["name"]
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