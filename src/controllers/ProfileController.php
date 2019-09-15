<?php


namespace app\controllers;


use framework\Database;
use framework\FormManager;
use framework\Router;
use framework\Tools;
use app\models\FamilyModel;
use app\models\PathModel;
use app\models\ProfileModel;

class ProfileController extends AbstractController
{

  public function indexAction()
  {
    $familyFilter = "*";
    if (FormManager::isSubmitted()) {
      $familyFilter = filter_input(INPUT_POST, "filter_family", FILTER_SANITIZE_STRING);
    }

    $profileList = [];
    try {
      if ($familyFilter !== "*") {
        $profileList = ProfileModel::getAllForFamily($familyFilter);
      } else {
        $profileList = ProfileModel::getAll();
      }
    } catch (\PDOException $ex) {
      Tools::setFlash("Erreur SQL" . $ex->getMessage(), "error");
    }

    $this->render("profile/index",
      [
        "title" => "Liste des profils",
        "families" => Tools::select(FamilyModel::getAll(), "famille", "description"),
        "familyFilter" => $familyFilter,
        "profileList" => $profileList
      ]);
  }

  public function editAction($id = null)
  {
    $profile = [];

    $form = new FormManager();
    $form
      ->setTitle("Maintenance des profils")
      ->addField(
        [
          "name" => "profil",
          "label" => "Identifiant",
          "errorMessage" => "Identifiant non saisi",
          "primeKey" => true
        ]
      )
      ->addField(
        [
          "name" => "nom",
          "label" => "Libellé",
          "errorMessage" => "Libellé non saisi"
        ]
      )
      ->addField(
        [
          "name" => "famille",
          "label" => "Famille",
          "errorMessage" => "Famille non choisie",
          "controlType" => "select",
          "valueList" => Tools::select(FamilyModel::getAll(), "famille", "description")
        ]
      )
      ->addField(
        [
          "name" => "type",
          "label" => "Type",
          "errorMessage" => "Type non choisi",
          "controlType" => "select",
          "valueList" => ProfileModel::getTypes()
        ]
      )
      ->setIndexRoute(Router::route(["profile", "index"]))
      ->setDeleteRoute(Router::route(["profile", "delete", $id]));

    if ($id) {
      $profile = ProfileModel::getOne($id);
    }

    if (FormManager::isSubmitted()) {
      if (Database::save(
        $form,
        $id,
        ProfileModel::class,
        [
          "insert" => "Le profil a été ajouté avec succès",
          "update" => "Le profil a été modifié avec succès"
        ])
      ) {
        if (FormManager::isSubmitted(["close"])) {
          Router::redirectTo(["profile", "index"]);
          return;
        }
      }
    }

    $this->render("profile/edit",
      [
        "profile" => $profile,
        "fm" => $form
      ]);
  }

  public function deleteAction($id = null)
  {
    $success = Database::remove($id,ProfileModel::class,
      [
        "success" => "Le profil a été supprimé avec succès",
        "failure" => "Cet identifiant de profil n'existe pas"
      ]);

    Router::redirectTo([($success ? "profile" : "home"), "index"]);
  }

  public function pathsAction($id)
  {
    $form = new FormManager();
    $form
      ->setTitle("Maintenance des voies de profils")
      ->addField(
        [
          "name" => "profil",
          "controlType" => "hidden",
          "primeKey" => true
        ]
      )
      ->addField(
        [
          "name" => "nom",
          "label" => "Profil",
          "primeKey" => true
        ]
      )
      ->setIndexRoute(Router::route(["profile", "index"]));

    $profile = ProfileModel::getOne($id);

    $paths = [];
    foreach (ProfileModel::getPaths($id) as $path) {
      $paths[] = $path["voie"];
    }

    if (FormManager::isSubmitted()) {
      $data = $form->getData();
      $voies = filter_input(INPUT_POST, "voies", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
      $data["voies"] = $voies;

      ProfileModel::savePaths($data);

      Tools::setFlash("La liste des voies du profil a été enregistrée avec succès");
      Router::redirectTo(["profile", "index"]);

      return;
    }

    $this->render("profile/paths",
      [
        "profile" => $profile,
        "voies" => $paths,
        "pathList" => Tools::select(PathModel::getAllForType(""), "voie", "nom"),
        "prestList" => Tools::select(PathModel::getAllForType("prest"), "voie", "nom"),
        "fm" => $form,
      ]);
  }
}