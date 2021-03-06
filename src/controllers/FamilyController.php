<?php


namespace app\controllers;

use framework\Database;
use framework\FormManager;
use framework\Router;
use framework\Tools;
use app\models\FamilyModel;

class FamilyController extends AbstractController
{

  public function indexAction()
  {
    $familyList = [];

    try {
      $familyList = FamilyModel::getAll();
    } catch (\PDOException $ex) {
      Tools::setFlash("Erreur SQL" . $ex->getMessage(),"danger");
    }

    $this->render("family/index",
      [
        "title" => "Liste des familles",
        "familyList" => $familyList
      ]);
  }

  public function editAction($id = null)
  {
    $family = [];

    $form = new FormManager();
    $form
      ->setTitle("Maintenance des familles")
      ->addField(
        [
          "name" => "famille",
          "label" => "Identifiant",
          "errorMessage" => "Identifiant non saisi",
          "primeKey" => true
        ])
      ->addField(
        [
          "name" => "description",
          "label" => "Description",
          "errorMessage" => "Description non saisie"
        ]
      )
      ->setIndexRoute(Router::route([ "family", "index" ]))
      ->setDeleteRoute(Router::route([ "family", "delete", $id ]))
    ;

    if ($id) {
      $family = FamilyModel::getOne($id);
    }

    if (FormManager::isSubmitted()) {
      if (Database::save(
        $form,
        $id,
        FamilyModel::class,
        [
          "insert" => "La famille a été ajoutée avec succès",
          "update" => "La famille a été modifiée avec succès"
        ])
      ) {
        if (FormManager::isSubmitted(["close"])) {
          Router::redirectTo(["family", "index"]);
          return;
        }
      }
    }

    $this->render("family/edit",
      [
        "family" => $family,
        "fm" => $form
      ]);
  }

  public function deleteAction($id = null)
  {

    $success = Database::remove($id, FamilyModel::class,
      [
        "success" => "La famille a été supprimée avec succès",
        "failure" => "Cet identifiant de famille n'existe pas"
      ]);

    Router::redirectTo([($success ? "family" : "home"), "index"]);
  }

}