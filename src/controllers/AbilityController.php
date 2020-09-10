<?php


namespace app\controllers;


use framework\Database;
use framework\FormManager;
use framework\Router;
use framework\Tools;
use app\models\AbilityModel;
use app\models\PathModel;

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
      Tools::setFlash("Erreur SQL" . $ex->getMessage(), "danger");
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
      ->setDeleteRoute(Router::route(["ability", "delete", $id]));

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
        if (FormManager::isSubmitted(["close"])) {
          Router::redirectTo(["ability", "index"]);
          return;
        }
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
    $success = Database::remove($id, AbilityModel::class,
      [
        "success" => "La capacité a été supprimée avec succès",
        "failure" => "Cet identifiant de capacité n'existe pas"
      ]);

    Router::redirectTo([($success ? "ability" : "home"), "index"]);
  }

  public function multipleAction()
  {

    $form = new FormManager();
    $form
      ->setTitle("Voie complète")
      ->addField([
        "name" => "path",
        "label" => "Voie",
        "controlType" => "select",
        "valueList" => Tools::select(
          PathModel::getAll(),
          "voie",
          "nom",
          true
        )
      ])
      ->addField(
        [
          "name" => "ranks",
          "label" => "Rangs",
          "controlType" => "number"
        ]
      )
      ->addField(
        [
          "name" => "fullPath",
          "label" => "Description de la voie",
          "controlType" => "textarea",
          "size" => [
            "cols" => 60,
            "rows" => 25
          ],
          "required" => true
        ]
      )
    ;

    if (FormManager::isSubmitted()) {
      $data = $form->getData();

      $path = $data["path"];
      $pathData = PathModel::getOne($path);
      $abilities = [];
      $abilities["voie"] = $path;

      $ranks = intval($data["ranks"] ?? "5");
      $abilities["rangs"] = $ranks++;

      $fullPath = " " . $data["fullPath"] . " {$ranks}. ";
      $slugs = [];
      for ($r = 1; $r <= $ranks - 1; $r++) {
        $startAt = strpos($fullPath, " {$r}. ");
        $nr = $r + 1;
        $endsAt = strpos($fullPath, " {$nr}. ");
        $rank = substr($fullPath, $startAt + 1, $endsAt - $startAt - 1);
        $rankParts = explode(" : ", $rank);
        $rankParts[0] = substr($rankParts[0],3);
        if (substr($rankParts[0], -1) === "*") {
          $spell = 1;
          $rankParts[0] = substr($rankParts[0], 0, strlen($rankParts[0]) - 1);
        }
        if (substr($rankParts[0], -4) === " (L)") {
          $limited = 1;
          $rankParts[0] = substr($rankParts[0], 0, strlen($rankParts[0]) - 4);
        }
        $slug = iconv('UTF-8','ASCII//TRANSLIT', $rankParts[0]);
        $slug = str_replace([ " ", "'", "`", "^" ], [ "-" ], $slug);
        $slug = strtolower($slug);
        if (!AbilityModel::getOne($slug)) {
          AbilityModel::insert([
            "capacite" => $slug,
            "nom" => $rankParts[0],
            "limitee" => $limited ?? 0,
            "sort" => $spell ?? 0,
            "type" => $pathData["type"],
            "description" => $rankParts[1]
          ]);
          Tools::setFlash("La capacité '{$rankParts[0]}' a été ajoutée avec succès", "success");
        } else {
          Tools::setFlash("L'identifiant de capacité '$slug' existe déjà", "warning");
        }
        $slugs[] = $slug;
      }
      $abilities["capacites"] = $slugs;
      PathModel::saveAbilities($abilities);
      Router::redirectTo(["ability", "index"]);
      return;
    }

    $this->render("ability/multiple",
      [
        "fm" => $form,
        "ranks" => 5
      ]);
  }
}