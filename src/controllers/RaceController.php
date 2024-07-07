<?php

namespace app\controllers;

use app\models\AbilityModel;
use app\models\RaceModel;
use framework\Database;
use framework\FormManager;
use framework\Router;
use framework\Tools;

class RaceController extends AbstractController
{

  public function indexAction()
  {
    $raceList = [];

    try {
      $raceList = RaceModel::getAll();
    } catch (\PDOException $ex) {
      Tools::setFlash("Erreur SQL" . $ex->getMessage(),"danger");
    }

    $this->render("race/index",
      [
        "title" => "Liste des races",
        "raceList" => $raceList
      ]);
  }

  public function editAction($id = null)
  {

    $race = [];

    $form = new FormManager();
    $form
      ->setTitle("Maintenance des races")
      ->addField(
        [
          "name" => "race",
          "label" => "Identifiant",
          "errorMessage" => "Identifiant non saisi",
          "primeKey" => true
        ]
      )
      ->addField(
        [
          "name" => "intitule",
          "label" => "Intitulé",
          "errorMessage" => "Intitulé non saisi",
          "required" => true
        ]
      )
      ->addField(
        [
          "name" => "mod_for",
          "label" => "FOR",
          "controlType" => "number"
        ]
      )
      ->addField(
        [
          "name" => "mod_dex",
          "label" => $_SESSION['dataset']['id'] == "cof2" ? "AGI" : "DEX",
          "controlType" => "number"
        ]
      )
      ->addField(
        [
          "name" => "mod_con",
          "label" => "CON",
          "controlType" => "number"
        ]
      )
      ->addField(
        [
          "name" => "mod_int",
          "label" => "INT",
          "controlType" => "number"
        ]
      )
      ->addField(
        [
          "name" => "mod_sag",
          "label" => $_SESSION['dataset']['id'] == "cof" ? "SAG" : "PER",
          "controlType" => "number"
        ]
      )
      ->addField(
        [
          "name" => "mod_vol",
          "label" => "VOL",
          "controlType" => "number"
        ]
      )
      ->addField(
        [
          "name" => "mod_cha",
          "label" => "CHA",
          "controlType" => "number"
        ]
      )
      ->addField(
        [
          "name" => "age_base",
          "label" => "Âge de départ",
          "controlType" => "number"
        ]
      )
      ->addField(
        [
          "name" => "esperance_vie",
          "label" => "Espérance de vie",
          "controlType" => "number"
        ]
      )
      ->addField(
        [
          "name" => "type_race",
          "label" => "Type",
          "errorMessage" => "Type non choisi",
          "controlType" => "select",
          "valueList" => RaceModel::getTypes()
        ]
      )
      ->addField(
        [
          "name" => "taille_min",
          "label" => "Taille min.",
          "controlType" => "decimal"
        ]
      )
      ->addField(
        [
          "name" => "taille_max",
          "label" => "Taille max.",
          "controlType" => "decimal"
        ]
      )
      ->addField(
        [
          "name" => "poids_min",
          "label" => "Poids min.",
          "controlType" => "number"
        ]
      )
      ->addField(
        [
          "name" => "poids_max",
          "label" => "Poids max.",
          "controlType" => "number"
        ]
      )
      ->setIndexRoute(Router::route([ "race", "index" ]))
      ->setDeleteRoute(Router::route([ "race", "delete", $id ]))
    ;

    if ($id) {
      $race = RaceModel::getOne($id);
    }

    if (FormManager::isSubmitted()) {
      if (Database::save(
        $form,
        $id,
        RaceModel::class,
        [
          "insert" => "La race a été ajoutée avec succès",
          "update" => "La race a été modifiée avec succès"
        ])
      ) {
        if (FormManager::isSubmitted(["close"])) {
          Router::redirectTo(["race", "index"]);
          return;
        }
      }
    }

    $this->render("race/edit",
      [
        "race" => $race,
        "fm" => $form
      ]
    );

  }

  public function traitsAction($id)
  {

    $form = new FormManager();
    $form
      ->setTitle("Maintenance des traits raciaux")
      ->addField(
        [
          "name" => "race",
          "controlType" => "hidden",
          "primeKey" => true
        ]
      )
      ->addField(
        [
          "name" => "intitule",
          "label" => "Race",
          "primeKey" => true
        ]
      )
      ->setIndexRoute(Router::route(["race", "index"]))
    ;

    $race = RaceModel::getOne($id);

    $traits = RaceModel::getTraits($id);
    if (count($traits) == 0) {
      $traits[] = [
        "intitule" => "Généralités",
        "description" => ""
      ];
      $traits[] = [
        "intitule" => "Description",
        "description" => ""
      ];
      $traits[] = [
        "intitule" => "Préjugés typiques",
        "description" => ""
      ];
      $traits[] = [
        "intitule" => "Noms typiques",
        "description" => ""
      ];
    }

    if (FormManager::isSubmitted()) {
      $data = $form->getData();

      $data["labels"] = filter_input(INPUT_POST, "labels", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
      $data["descriptions"] = filter_input(INPUT_POST, "descriptions", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

      RaceModel::saveTraits($data);

      Tools::setFlash("La liste de traits raciaux a été enregistrée avec succès", "success");
      Router::redirectTo(["race", "index"]);
      return;
    }

    $this->render("race/traits",
      [
        "race" => $race,
        "traits" => $traits,
        "fm" => $form,
      ]
    );

  }

  public function abilitiesAction($id)
  {
    $form = new FormManager();
    $form
      ->setTitle("Maintenance des capacités raciales")
      ->addField(
        [
          "name" => "race",
          "controlType" => "hidden",
          "primeKey" => true
        ]
      )
      ->addField(
        [
          "name" => "intitule",
          "label" => "Race",
          "primeKey" => true
        ]
      )
      ->setIndexRoute(Router::route(["race", "index"]))
    ;

    $race = RaceModel::getOne($id);

    $abilities = [];
    foreach (RaceModel::getAbilities($id) as $ability) {
      $abilities[] = $ability["capacite"];
    }

    if (FormManager::isSubmitted()) {
      $data = $form->getData();
      $abilities = filter_input(INPUT_POST, "abilities", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
      $data["abilities"] = $abilities;

      RaceModel::saveAbilities($data);

      Tools::setFlash("La liste des capacités raciales a été enregistrée avec succès", "success");
      Router::redirectTo(["race", "index"]);

      return;
    }

    $this->render("race/abilities",
      [
        "race" => $race,
        "abilities" => $abilities,
        "abilityList" => Tools::select(AbilityModel::getAllForType("race"), "capacite", "nom"),
        "fm" => $form,
      ]);
  }
}