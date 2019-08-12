<?php


namespace m2i\project\controllers;


use m2i\framework\FormManager;
use m2i\framework\Router;
use m2i\framework\Tools;
use m2i\project\models\PathModel;
use m2i\project\models\ProfileModel;

class PathController extends AbstractController
{

  public function indexAction()
  {
    $pathType="*";
    if (FormManager::isSubmitted()) {
      $pathType = filter_input(INPUT_POST, "filter_type", FILTER_SANITIZE_STRING);
    }

    $pathList = [];

    try {
      if ($pathType !== "*") {
        $pathList = PathModel::getAllForType($pathType);
      } else {
        $pathList = PathModel::getAll();
      }
    } catch (\PDOException $ex) {
      Tools::setFlash("Erreur SQL" . $ex->getMessage(), "error");
    }

    $this->render("path/index",
      [
        "title" => "Liste des voies",
        "pathTypes" => PathModel::getTypes(),
        "pathType" => $pathType,
        "pathList" => $pathList
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
          "name" => "type",
          "label" => "Type",
          "errorMessage" => "Type non choisi",
          "controlType" => "select",
          "valueList" => [
            "0" => "Base",
            "1" => "Hybride"
          ]
        ]
      )
      ->setIndexRoute(Router::route(["profile", "index"]))
      ->setDeleteRoute(Router::route(["profile", "delete"]));

    if ($id) {
      $profile = ProfileModel::getOne($id);
    }

    if (FormManager::isSubmitted()) {
      if ($form->isValid()) {
        $message = null;
        $data = $form->getData();
        if ($id) {
          try {
            ProfileModel::update($data);
            $message = "Le profil {$data['nom']} a été modifié avec succès";
          } catch (\PDOException $ex) {
            Tools::setFlash("Erreur SQL" . $ex->getMessage(), "error");
            return;
          }
        } else {
          try {
            ProfileModel::insert($data);
            $message = "Le profil {$data['nom']} a été ajouté avec succès";
          } catch (\PDOException $ex) {
            Tools::setFlash("Erreur SQL" . $ex->getMessage(), "error");
            return;
          }
        }
        if ($message) {
          Tools::setFlash($message);
        }
        Router::redirectTo(["profile", "index"]);
        return;
      } else {
        $errors = $form->validateForm();
        Tools::setFlash($errors);
      }
    }

    $this->render("profile/edit",
      [
        "profile" => $profile,
        "fm" => $form
      ]);
  }
}