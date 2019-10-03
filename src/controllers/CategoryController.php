<?php


namespace app\controllers;


use app\models\CategoryModel;
use app\models\PropertyModel;
use framework\Database;
use framework\FormManager;
use framework\Router;
use framework\Tools;

class CategoryController extends AbstractController
{

  public function indexAction()
  {
    $categoryList = [];

    try {
      $categoryList = CategoryModel::getAllWithMain();
    } catch (\PDOException $ex) {
      Tools::setFlash("Erreur SQL" . $ex->getMessage(),"error");
    }

    $this->render("category/index",
      [
        "title" => "Liste des catégories d'équipement",
        "categoryList" => $categoryList
      ]);
  }

  public function editAction($id = null)
  {
    $category = [];

    $form = new FormManager();
    $form
      ->setTitle("Maintenance des catégories")
      ->addField(
        [
          "name" => "code",
          "label" => "Identifiant",
          "errorMessage" => "Identifiant non saisi",
          "primeKey" => true
        ])
      ->addField(
        [
          "name" => "libelle",
          "label" => "Libellé",
          "errorMessage" => "Libellé non saisi"
        ]
      )
      ->addField(
        [
          "name" => "parent",
          "label" => "Catégorie parente",
          "errorMessage" => "Catégorie parente non saisie",
          "controlType" => "select",
          "valueList" => array_merge(
            [ "" => "Choisir une catégorie..." ],
            Tools::select(CategoryModel::getAllMain(), "code", "libelle")
          )
        ]
      )
      ->setIndexRoute(Router::route([ "category", "index" ]))
      ->setDeleteRoute(Router::route([ "category", "delete" ]))
    ;

    if ($id) {
      $category = CategoryModel::getOne($id);
    }

    $properties = [];
    foreach (CategoryModel::getProperties($id) as $property) {
      $properties[] = $property["code_propriete"];
    }

    if (FormManager::isSubmitted()) {
      if (Database::save(
        $form,
        $id,
        CategoryModel::class,
        [
          "insert" => "La catégorie a été ajoutée avec succès",
          "update" => "La catégorie a été modifiée avec succès"
        ])
      ) {
        $data = $form->getData();
        $properties = filter_input(INPUT_POST, "properties", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $data["properties"] = $properties;
        CategoryModel::saveProperties($data);
        if (FormManager::isSubmitted(["close"])) {
          Router::redirectTo(["category", "index"]);
          return;
        }
      }
    }

    $this->render("category/edit",
      [
        "category" => $category,
        "properties" => $properties,
        "propList" => Tools::select(PropertyModel::getAll(), "code", "intitule"),
        "fm" => $form
      ]);
  }

  public function deleteAction($id = null)
  {

    $success = Database::remove($id, CategoryModel::class,
      [
        "success" => "La catégorie a été supprimée avec succès",
        "failure" => "Cet identifiant de catégorie n'existe pas"
      ]);

    Router::redirectTo([($success ? "category" : "home"), "index"]);
  }

}