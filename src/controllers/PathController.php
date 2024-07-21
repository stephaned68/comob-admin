<?php


namespace app\controllers;

use framework\Database;
use framework\FormManager;
use framework\Router;
use framework\Tools;
use app\models\AbilityModel;
use app\models\PathModel;

class PathController extends AbstractController
{

  public function indexAction()
  {
    $pathType = "*";
    if (FormManager::isSubmitted()) {
      $pathType = filter_input(INPUT_POST, "filter_type", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    $pathList = [];

    try {
      if ($pathType !== "*") {
        $pathList = PathModel::getAllForType($pathType);
      } else {
        $pathList = PathModel::getAll();
      }
    } catch (\PDOException $ex) {
      Tools::setFlash("Erreur SQL" . $ex->getMessage(), "danger");
    }

    $this->render("path/index",
      [
        "title" => "Liste des voies",
        "pathTypes" => PathModel::getTypes(),
        "pathType" => $pathType,
        "pathList" => $pathList,
        "fm" => $this->editForm(),
        "path" => [],
      ]);
  }

  public function editForm() : FormManager
  {
    $form = new FormManager();
    $form
      ->setTitle("Maintenance des voies")
      ->addField(
        [
          "name" => "voie",
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
          "name" => "notes",
          "label" => "Notes",
          "controlType" => "textarea",
          "size" => [
            "cols" => 60,
            "rows" => 4
          ]
        ]
      )
      ->addField(
        [
          "name" => "equipement",
          "label" => "Equipement",
          "controlType" => "textarea",
          "size" => [
            "cols" => 60,
            "rows" => 4
          ]
        ]
      )
      ->addField(
        [
          "name" => "type",
          "label" => "Type",
          "errorMessage" => "Type non choisi",
          "controlType" => "select",
          "valueList" => PathModel::getTypes()
        ]
      )
      ->addField(
        [ // <Troumad>
          "name" => "pfx_deladu",
          "label" => "Préfixe",
          "errorMessage" => "Préfixe non choisi",
          "controlType" => "select",
          "valueList" => [
            "0" => "Voie du",
            "1" => "Voie de la",
            "2" => "Voie de l'",
            "3" => "Voie des",
          ]
        ] // </Troumad>
      )
      ->setDeleteRoute(Router::route(["path", "delete", ""]));

    return $form;
  }

  public function getAction($id)
  {
    $path = PathModel::getOne($id);
    echo json_encode($path);
  }

  public function editAction($id = null)
  {

    $path = [];
    if ($id) {
      $path = PathModel::getOne($id);
    }

    $form = new FormManager();
    $form
      ->setTitle("Maintenance des voies")
      ->addField(
        [
          "name" => "voie",
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
          "name" => "notes",
          "label" => "Notes",
          "controlType" => "textarea",
          "size" => [
            "cols" => 60,
            "rows" => 4
          ]
        ]
      )
      ->addField(
        [
          "name" => "equipement",
          "label" => "Equipement",
          "controlType" => "textarea",
          "size" => [
            "cols" => 60,
            "rows" => 4
          ]
        ]
      )
      ->addField(
        [
          "name" => "type",
          "label" => "Type",
          "errorMessage" => "Type non choisi",
          "controlType" => "select",
          "valueList" => PathModel::getTypes()
        ]
      )
      ->addField(
        [ // <Troumad>
          "name" => "pfx_deladu",
          "label" => "Préfixe",
          "errorMessage" => "Préfixe non choisi",
          "controlType" => "select",
          "valueList" => [
            "0" => "Voie du",
            "1" => "Voie de la",
            "2" => "Voie de l'",
            "3" => "Voie des",
          ]
        ] // </Troumad>
      )
      ->setIndexRoute(Router::route(["path", "index"]))
      ->setDeleteRoute(Router::route(["path", "delete", $id]));

    if (FormManager::isSubmitted()) {
      if (Database::save(
        $form,
        $id,
        PathModel::class,
        [
          "insert" => "La voie a été ajoutée avec succès",
          "update" => "La voie a été modifiée avec succès"
        ])
      ) {
        if (FormManager::isSubmitted(["close"])) {
          Router::redirectTo(["path", "index"]);
          return;
        }
      }
    }

    $this->render("path/edit",
      [
        "path" => $path,
        "fm" => $form,
      ]);
  }

  public function deleteAction($id = null)
  {
    $success = Database::remove($id,PathModel::class,
      [
        "success" => "La voie a été supprimée avec succès",
        "failure" => "Cet identifiant de voie n'existe pas"
      ]);

    Router::redirectTo([($success ? "path" : "home"), "index"]);
  }

  public function abilitiesAction($id)
  {
    $form = new FormManager();
    $form
      ->setTitle("Maintenance des capacités par voies")
      ->addField(
        [
          "name" => "voie",
          "controlType" => "hidden",
          "primeKey" => true
        ]
      )
      ->addField(
        [
          "name" => "nom",
          "label" => "Voie",
          "primeKey" => true
        ]
      )
      ->addField(
        [
          "name" => "rangs",
          "label" => "Rangs",
          "controlType" => "number"
        ]
      )
      ->setIndexRoute(Router::route(["path", "index"]));

    $path = PathModel::getOne($id);
    if (!$path) {
      Tools::setFlash("La voie $id n'inexiste pas", "warning");
      Router::redirectTo(["race"]);
      return;
    }
    $maxRanks = 5;
    if ($path["type"] !== null && $path["type"] !== "") {
      $pathType = PathModel::getOneType($path["type"]);
      $pathData = $pathType["type_voie_config"];
      if ($pathData !== null && $pathData !== "") {
        $pathConfig = json_decode($pathData, true);
        $maxRanks = $pathConfig["ranks"] ?? 5;
      }
    }

    $abilities = [];
    foreach (PathModel::getAbilities($id) as $ability) {
      $abilities[] = $ability["capacite"];
    }
    $path["rangs"] = count($abilities);
    if ($path["rangs"] !== $maxRanks) {
      $path["rangs"] = $maxRanks;
    }
    while (count($abilities) < $maxRanks) { // < 5
      $abilities[] = "";
    }

    if (FormManager::isSubmitted()) {
      $data = $form->getData();
      $capacites = filter_input(INPUT_POST, "capacites", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
      $data["capacites"] = $capacites;

      PathModel::saveAbilities($data);

      Tools::setFlash("La liste des capacités de la voie a été enregistrée avec succès", "success");
      Router::redirectTo(["path", "index"]);

      return;
    }

    $this->render("path/abilities",
      [
        "path" => $path,
        "capacites" => $abilities,
        "abilityList" => Tools::select(AbilityModel::getAllForType($path["type"]), "capacite", "nom"),
        "maxRanks" => $maxRanks,
        "fm" => $form,
      ]);
  }
}
