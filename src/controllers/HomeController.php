<?php

namespace app\controllers;


use framework\Database;
use framework\FormManager;
use framework\Router;
use framework\Tools;
use Symfony\Component\VarDumper\Cloner\Data;

class HomeController extends AbstractController
{

  public function indexAction() : void
  {
    $this->render("home/index", [ ]);
  }

  public function selectAction() : void
  {

    if (FormManager::isSubmitted()) {
      $dataset = $_POST["dataset"];
      if ($dataset != $_SESSION["dataset"]["id"]) {
        $_SESSION["dataset"] = [
          "id" => $dataset,
          "name" => DATASETS[$dataset]["name"]
        ];
      }
      Router::redirectTo(["home", "index"]);
      return;
    }

    $this->render("home/select",
      [
        "title" => "Catalogues"
      ]
    );

  }

  public function checkDBAction($dataset = "") : void
  {
    if ($dataset == "") $dataset = $_SESSION["dataset"]["id"];
    $sql = implode(" ", [
      "SHOW TABLES",
      "FROM `" . DBNAME . "`",
      "WHERE `Tables_in_" . DBNAME . "` LIKE '" . $dataset . "_%'"
    ]);
    $tables = Database::raw($sql);
    $tableNames = [];
    $found = [];
    foreach($tables as $table) {
      $tableName = $table["Tables_in_" . DBNAME];
      $tableNames[] = $tableName;
      $columns = implode(", ", Database::getColumnsList($tableName, "char"));
      $sql = implode(" ", [
        "SELECT concat_ws(', ', " . $columns . ") AS data_record",
        "FROM `$tableName`"
      ]);
      $rows = Database::raw($sql);
      if (count($rows) == 0) continue;
      foreach ($rows as $row) {
        if (preg_match("/&.*;/", $row["data_record"]) == 0) continue;
        $found[] = $tableName." > ".htmlspecialchars($row["data_record"]);
      }
    }

    $foundNb = count($found);
    if ($foundNb == 0) {
      Tools::setFlash("Pas d'anomalie détectée", "success");
    } else {
      Tools::setFlash("$foundNb échappements HTML détectées", "warning");
    }

    $this->render("home/checkdb", [
      "tableNames" => $tableNames,
      "found" => $found
    ]);
  }
}