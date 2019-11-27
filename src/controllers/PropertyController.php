<?php


namespace app\controllers;


use app\models\CategoryModel;
use app\models\Property;
use app\models\PropertyModel;
use framework\Database;
use framework\FormManager;
use framework\Router;
use framework\Tools;

class PropertyController extends AbstractController
{

  public function indexAction()
  {
    $propertyList = [];

    try {
      $propertyList = PropertyModel::getAll();
    } catch (\PDOException $ex) {
      Tools::setFlash("Erreur SQL" . $ex->getMessage(),"error");
    }

    $this->render("property/index",
      [
        "title" => "Liste des propriétés d'équipement",
        "propertyList" => $propertyList
      ]);
  }

  public function editAction($id = null)
  {

    $property = new Property();

    $form = new FormManager();
    $form
      ->setTitle("Maintenance des propriétés")
      ->setEntity(Property::class)
      ->addField(
        [
          "name" => "code",
          "label" => "Identifiant",
          "errorMessage" => "Identifiant non saisi",
          "primeKey" => true
        ])
      ->addField(
        [
          "name" => "intitule",
          "label" => "Intitulé",
          "errorMessage" => "Intitulé non saisi"
        ]
      )
      ->addField(
        [
          "name" => "defaut",
          "label" => "Valeur par défaut"
        ]
      )
      ->setIndexRoute(Router::route([ "property", "index" ]))
      ->setDeleteRoute(Router::route([ "property", "delete" ]))
    ;

    if ($id) {
      $property = PropertyModel::getOne($id);
    }

    if (FormManager::isSubmitted()) {
      if (Database::save(
        $form,
        $id,
        PropertyModel::class,
        [
          "insert" => "La propriété a été ajoutée avec succès",
          "update" => "La propriété a été modifiée avec succès"
        ])
      ) {
        if (FormManager::isSubmitted(["close"])) {
          Router::redirectTo(["property", "index"]);
          return;
        }
      }
    }

    $this->render("property/edit",
      [
        "property" => $property,
        "fm" => $form
      ]);

  }

}