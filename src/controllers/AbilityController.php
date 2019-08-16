<?php


namespace m2i\project\controllers;


use m2i\framework\Database;
use m2i\framework\FormManager;
use m2i\framework\Router;
use m2i\framework\Tools;
use m2i\project\models\AbilityModel;
use m2i\project\models\PathModel;

class AbilityController extends AbstractController
{

  public function indexAction()
  {
    $abilityType = "*";
    if (FormManager::isSubmitted()) {
      $abilityType = filter_input(INPUT_POST, "filter_type", FILTER_SANITIZE_STRING);
    }

    $abilityList = [];

    try {
      if ($abilityType !== "*") {
        $abilityList = AbilityModel::getAllForType($abilityType);
      } else {
        $abilityList = AbilityModel::getAll();
      }
    } catch (\PDOException $ex) {
      Tools::setFlash("Erreur SQL" . $ex->getMessage(), "error");
    }

    $this->render("ability/index",
      [
        "title" => "Liste des capacités",
        "abilityTypes" => AbilityModel::getTypes(),
        "abilityType" => $abilityType,
        "abilityList" => $abilityList
      ]);

  }

  public function editAction($id = null)
  {
    $ability = [];

    $form = new FormManager();
    $form
      ->setTitle("Maintenance des capacités")
      ->addField(
        [
          "name" => "capacite",
          "label" => "Identifiant",
          "errorMessage" => "Identifiant non saisi",
          "primeKey" => true
        ]
      )
      ->addField(
        [
          "name" => "nom",
          "label" => "Libellé",
          "errorMessage" => "Libellé non saisi",
          "required" => true
        ]
      )
      ->addField(
        [
          "name" => "limitee",
          "label" => "Action limitée ?",
          "controlType" => "checkbox"
        ]
      )
      ->addField(
        [
          "name" => "sort",
          "label" => "Sort ?",
          "controlType" => "checkbox"
        ]
      )
      ->addField(
        [
          "name" => "type",
          "label" => "Type",
          "errorMessage" => "Type non choisi",
          "controlType" => "select",
          "valueList" => AbilityModel::getTypes()
        ]
      )
      ->addField(
        [
          "name" => "description",
          "label" => "Description",
          "controlType" => "textarea",
          "size" => [
            "cols" => 60,
            "rows" => 12
          ],
          "required" => true
        ]
      )
      ->setIndexRoute(Router::route(["ability", "index"]))
      ->setDeleteRoute(Router::route(["ability", "delete", $id]))
    ;

    if ($id) {
      $ability = AbilityModel::getOne($id);
    }

    if (FormManager::isSubmitted()) {
      if (Database::save(
        $form,
        $id,
        AbilityModel::class,
        [
          "insert" => "La capacité a été ajoutée avec succès",
          "update" => "La capacité a été modifiée avec succès"
        ])
      ) {
        Router::redirectTo(["ability", "index"]);
        return;
      }
    }

    $this->render("ability/edit",
      [
        "ability" => $ability,
        "fm" => $form
      ]);
  }

  public function deleteAction($id = null)
  {
    $success = Database::remove($id,AbilityModel::class,
      [
        "success" => "La capacité a été supprimée avec succès",
        "failure" => "Cet identifiant de capacité n'existe pas"
      ]);

    Router::redirectTo([($success ? "ability" : "home"), "index"]);
  }
}