<?php


namespace app\controllers;

use app\models\CategoryModel;
use app\models\EquipmentModel;
use app\models\PropertyModel;
use framework\Database;
use framework\FormManager;
use framework\Router;
use framework\Tools;

class EquipmentController extends AbstractController
{

  public function indexAction()
  {
    $categoryFilter = "*";
    if (FormManager::isSubmitted()) {
      $categoryFilter = filter_input(INPUT_POST, "filter_category", FILTER_SANITIZE_STRING);
    }

    $equipmentList = [];

    try {
      if ($categoryFilter !== "*") {
        $equipmentList = EquipmentModel::getByCategoryWithProps($categoryFilter);
      } else {
        $equipmentList = EquipmentModel::getByCategoryWithProps();
      }
    } catch (\PDOException $ex) {
      Tools::setFlash("Erreur SQL" . $ex->getMessage(),"danger");
    }

    $this->render("equipment/index",
      [
        "title" => "Liste d'équipements",
        "categoryFilter" => $categoryFilter,
        "categories" => Tools::select(CategoryModel::getAllSubWithMain(), "code", "libelle"),
        "equipmentList" => $equipmentList
      ]);
  }

  public function editAction($id = null)
  {
    $equipment = [];

    $form = new FormManager();
    $form
      ->setTitle("Maintenance des équipements")
      ->addField(
        [
          "name" => "code",
          "label" => "Identifiant",
          "errorMessage" => "Identifiant non saisi",
          "primeKey" => true
        ]
      )
      ->addField(
        [
          "name" => "designation",
          "label" => "Désignation",
          "errorMessage" => "Désignation non saisie"
        ]
      )
      ->addField(
        [
          "name" => "categorie",
          "label" => "Catégorie",
          "errorMessage" => "Catégorie non saisie",
          "controlType" => "select",
          "valueList" => array_merge(
            [ "" => "Choisir une catégorie..." ],
            Tools::select(CategoryModel::getAllSubWithMain(), "code", "libelle")
          )
        ]
      )
      ->addField(
        [
          "name" => "sequence",
          "label" => "Séquence"
        ]
      )
      ->addField(
        [
          "name" => "prix",
          "label" => "Prix",
          "controlType" => "number",
          "errorMessage" => "Prix non saisi"
        ]
      )
      ->addField(
        [
          "name" => "notes",
          "label" => "Notes",
          "controlType" => "textarea",
          "size" => [
            "cols" => 60,
            "rows" => 5
          ]
        ]
      )
      ->setIndexRoute(Router::route([ "equipment", "index" ]))
      ->setDeleteRoute(Router::route([ "equipment", "delete", $id ]))
      ;

    if ($id) {
      $equipment = EquipmentModel::getOne($id);
    }

    if (FormManager::isSubmitted()) {
      $success = Database::save(
        $form,
        $id,
        EquipmentModel::class,
        [
          "insert" => "L'équipement a été ajouté avec succès",
          "update" => "L'équipement a été modifié avec succès"
        ]);
      if ($success) {
        if (FormManager::isSubmitted(["close"])) {
          Router::redirectTo(["equipment", "index"]);
          return;
        }
      }
    }

    $this->render("equipment/edit",
      [
        "equipment" => $equipment,
        "fm" => $form
      ]);
  }

  public function deleteAction($id = null)
  {

    $success = Database::remove($id, EquipmentModel::class,
      [
        "success" => "L'équipement a été supprimé avec succès",
        "failure" => "Cet identifiant d'équipement n'existe pas",
        "integrity" => "Echec de la suppression de l'équipement : supprimer d'abord les propriétés liées."
      ]);

    Router::redirectTo([($success ? "equipment" : "home"), "index"]);
  }

  public function propertiesAction($id)
  {

    $equipment = EquipmentModel::getOne($id);
    $category = CategoryModel::getOne($equipment["categorie"]);
    $propertyList = PropertyModel::getByCategory($category["code"]);

    $propertyValues = EquipmentModel::getProperties($id);
    $properties = [];
    foreach ($propertyValues as $propertyValue) {
      $properties[$propertyValue["code_propriete"]] = $propertyValue["valeur"];
    }

    $mainForm = new FormManager();
    $mainForm
      ->setTitle("Maintenance des propriétés d'équipements")
      ->addField(
        [
          "name" => "code",
          "controlType" => "hidden",
          "primeKey" => true
        ]
      )
      ->addField([
        "name" => "designation",
        "label" => "Equipement",
        "primeKey" => true
      ])
      ->setIndexRoute(Router::route(["equipment", "index"]))
    ;

    $propsForm = new FormManager();
    foreach ($propertyList as $property) {
      $propsForm->addField(
        [
          "name" => $property["code"],
          "label" => $property["intitule"],
          "errorMessage" => $property["intitule"] . " non saisi(e.s)",
          "defaultValue" => $property["defaut"]
        ]
      );
    }

    if (FormManager::isSubmitted()) {

      $data = $mainForm->getData();
      $data["props"] = $propsForm->getData();

      EquipmentModel::saveProperties($data);

      Tools::setFlash("Les propriétés de l'équipement ont été enregistrées avec succès", "success");
      Router::redirectTo(["equipment", "index"]);

      return;
    }

    $this->render("equipment/properties",
      [
        "equipment" => $equipment,
        "main" => $mainForm,
        "properties" => $properties,
        "props" => $propsForm
      ]);

  }

}