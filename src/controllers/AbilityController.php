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
      $abilityType = filter_input(INPUT_POST, "filter_type", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
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
          "label" => ($_SESSION["dataset"]["id"] == 'cog') ? "Vaisseau" : "Sort",
          "controlType" => "checkbox"
        ]
      )
      ->addField(
        [
          "name" => "action",
          "label" => "Action",
          "controlType" => "select",
          "valueList" => [
            "0" => "",
            "1" => "Gratuite",
            "2" => "Mouvement",
            "3" => "Action"
          ]
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

  private function processAbilityName(array $params) : array
  {
    $abilityName = "";
    $extra = "";
    $limited = 0;
    $spell = 0;
    $action = 0;
    $dataset = $_SESSION["dataset"]["id"];

    extract($params);

    if (substr($abilityName, -11) === "(G) ou (L)*" ||
        substr($abilityName, -11) === "(M) ou (L)*" ||
        substr($abilityName, -11) === "(A) ou (L)*" ||
        substr($abilityName, -11) === "(G) ou (M)*" ||
        substr($abilityName, -11) === "(G) ou (A)*" ||
        substr($abilityName, -11) === "(M) ou (A)*") {
      $spell = 1;
      $limited = substr($abilityName, -3, 1) === "L" ? 1 : 0;
      $action = match(substr($abilityName, -10, 1) . substr($abilityName, -3, 1)) {
        "GL" => 1,
        "ML" => 2,
        "AL" => 3,
        "GM" => 2,
        "GA" => 3,
        "MA" => 3,
        default => 0
      };
      $abilityName = substr($abilityName, 0, strlen($abilityName) - 11);
    }

    if (substr($abilityName, -1) === "*") {
      $spell = 1;
      $abilityName = substr($abilityName, 0, strlen($abilityName) - 1);
    }
    if (substr($abilityName, -5) === " (L*)") {
      $limited = 1;
      $spell = 1;
      $abilityName = substr($abilityName, 0, strlen($abilityName) - 5);
    }
    if (substr($abilityName, -4) === " (L)") {
      $limited = 1;
      $abilityName = substr($abilityName, 0, strlen($abilityName) - 4);
    }
    if (substr($abilityName, -10) === " (L - PER)") {
      $limited = 1;
      $extra = " (PER)";
      $abilityName = substr($abilityName, 0, strlen($abilityName) - 10);
    }
    if (substr($abilityName, -10) === " (L - CHA)") {
      $limited = 1;
      $extra = " (CHA)";
      $abilityName = substr($abilityName, 0, strlen($abilityName) - 10);
    }
    if (substr($abilityName, -5) === " (A*)") {
      $spell = 1;
      $extra = " (A)";
      $abilityName = substr($abilityName, 0, strlen($abilityName) - 5);
    }
    if (substr($abilityName, -4) === " (A)") {
      $extra = $dataset === "cof2" ? "" : " (A)";
      $action = $dataset === "cof2" ? 3 : 0;
      $abilityName = substr($abilityName, 0, strlen($abilityName) - 4);
    }
    if (substr($abilityName, -10) === " (A - PER)") {
      $extra = " (A:PER)";
      $abilityName = substr($abilityName, 0, strlen($abilityName) - 10);
    }
    if (substr($abilityName, -10) === " (A - CHA)") {
    $extra = " (A:CHA)";
    $abilityName = substr($abilityName, 0, strlen($abilityName) - 10);
    }
    if (substr($abilityName, -5) === " (M*)") {
      $spell = 1;
      $extra = " (M)";
      $abilityName = substr($abilityName, 0, strlen($abilityName) - 5);
    }
    if (substr($abilityName, -4) === " (M)") {
      $extra = $dataset === "cof2" ? "" : " (M)";
      $action = $dataset === "cof2" ? 2 : 0;
      $abilityName = substr($abilityName, 0, strlen($abilityName) - 4);
    }
    if (substr($abilityName, -10) === " (M - PER)") {
      $extra = " (M:PER)";
      $abilityName = substr($abilityName, 0, strlen($abilityName) - 10);
    }
    if (substr($abilityName, -10) === " (M - CHA)") {
      $extra = " (M:CHA)";
      $abilityName = substr($abilityName, 0, strlen($abilityName) - 10);
    }
    if (substr($abilityName, -4) === " (G)") {
      $action = $dataset === "cof2" ? 1 : 0;
      $abilityName = substr($abilityName, 0, strlen($abilityName) - 4);
    }

    return compact([ "abilityName", "extra", "limited", "spell", "action" ]);
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
      $prestcof2 = ($_SESSION["dataset"]["id"] === "cof2" && $pathData["type"] === "prest") ? 3 : 0;

      $ranks = intval($data["ranks"] ?? "5");
      $lastRank = $ranks + $prestcof2 + 1;
      $abilities["rangs"] = $ranks;

      $fullPath = " " . $data["fullPath"] . " R#{$lastRank}. ";
      $slugs = [];
      for ($r = 1; $r <= $ranks; $r++) {
        // get rank
        $searchRank = $r + $prestcof2;
        $startAt = strpos($fullPath, " R#{$searchRank}. ");
        $nextRank = $searchRank + 1;
        $endsAt = strpos($fullPath, " R#{$nextRank}. ");
        $rank = substr($fullPath, $startAt + 1 + 2, $endsAt - $startAt - 2);
        $fullPath = substr($fullPath, $endsAt - 1);
        $rankParts = explode(" : ", $rank);
        // process ability name
        $abilityName = substr($rankParts[0],3);
        $extra = "";
        $limited = 0;
        $spell = 0;
        $action = 0;
        $result = $this->processAbilityName(compact([ "abilityName", "extra", "limited", "spell", "action" ]));
        extract($result);
        // create slug
        $slug = $abilityName;
        $slug = str_replace("héroïque", "hero", $slug);
        $slug = iconv('UTF-8','ASCII//TRANSLIT', $slug);
        $slug = str_replace([ " ", "'", "`", "^", "/" ], [ "-" ], $slug);
        $slug = str_replace("-(o)", "", strtolower($slug));
        $slug = strtr($slug, [
          "force" => "for",
          "agilite" => "agi",
          "constitution" => "con",
          "perception" => "per",
          "intelligence" => "int",
          "volonte" => "vol",
          "charisme" => "cha"
        ]);
        if (strlen($slug) > 20) {
          $slug = strtr($slug, [
            "-a-" => "-",
            "-d-" => "-",
            "-du-" => "-",
            "-de-la-" => "-",
            "-de-" => "-",
            "-des-" => "-",
            "-l-" => "-",
            "-le-" => "-",
            "-la-" => "-",
            "-les-" => "-",
          ]);
          if (strlen($slug) > 20)
            $slug = substr($slug, 0, 20);
        }
        if ($action > 0) {
          $slug = substr($slug, 0, 18) . match($action) {
            1 => "-g",
            2 => "-m",
            3 => "-a",
            default => ""
          };
        }
        if (!AbilityModel::getOne($slug)) {
          $description = trim($rankParts[1]);
          if (count($rankParts) > 2)
            $description .= " : " . $rankParts[2];
          $data = [
            "capacite" => $slug,
            "nom" => $abilityName . $extra,
            "limitee" => $limited ?? 0,
            "sort" => $spell ?? 0,
            "type" => $pathData["type"],
            "description" => $description,
            "action" => $action
          ];
          AbilityModel::insert($data);
          Tools::setFlash("La capacité '{$abilityName}' a été ajoutée avec succès", "success");
        } else {
          Tools::setFlash("L'identifiant de capacité '$slug' existe déjà", "warning");
          $slug = "";
        }
        $slugs[] = $slug;
      }
      $abilities["capacites"] = $slugs;
      PathModel::saveAbilities($abilities);
      Router::redirectTo(["path", "abilities", $path]);
      return;
    }

    $this->render("ability/multiple",
      [
        "fm" => $form,
        "ranks" => 5
      ]);
  }
}