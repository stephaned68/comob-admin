<?php


namespace app\models;

use framework\Database;
use framework\EntityManager;
use framework\Tools;

class EquipmentModel
{
  public static $table = "equipement";

  public static function getAll()
  {
    $rs = Database::getPDO()->query(
      Database::getAllQuery(self::$table)
    );
    return $rs->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getByCategory($category=null)
  {
    $sql = implode(
      " ",
      [
        "select",
        "e.code as code,",
        "e.designation as designation,",
        "c.libelle as categorie",
        "from " . Database::table(self::$table) . " as e",
        "left join ". Database::table(CategoryModel::$table) . " as c",
        "on c.code = e.categorie",
        ($category != null ? "where e.categorie = '$category'" : ""),
        "order by c.code, e.code"
      ]
    );

    $rs = Database::getPDO()->query(implode(
      " ",
      [
        "select * from {$_SESSION['dataset']['id']}_vu_equipment_getbycategory",
        ($category != null ? "where categorie = '$category'" : "")
      ]
    ));
    return $rs->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getByCategoryWithProps($category=null)
  {
    $view = "vu_equipment_" . __FUNCTION__;
    if (Database::exists($view)) {
      $sql = implode(
        " ",
        [
          "select * from {$_SESSION['dataset']['id']}_{$view}",
          ($category != null ? "where categorie = '$category'" : "")
        ]
      );
    } else {
      $sql = implode(
        " ",
        [
          "select",
          "e.code as code,",
          "e.designation as designation,",
          "c.libelle as categorie,",
          "e.prix as prix,",
          "group_concat(concat_ws(' : ', pe.intitule, ep.valeur) separator '\n ') as props",
          "from " . Database::table(self::$table) . " as e",
          "join " . Database::table(CategoryModel::$table) . " as c",
          "on c.code = e.categorie",
          "left join cof_equipement_proprietes as ep",
          "on ep.code_equipement = e.code",
          "left join cof_proprietes_equipement as pe",
          "on pe.code = ep.code_propriete",
          ($category != null ? "where e.categorie = '$category'" : ""),
          "group by c.code, e.code",
          "order by c.code, e.code"
        ]
      );
    }

    $rs = Database::getPDO()->query($sql);
    return $rs->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getOne($id)
  {
    $statement = Database::getPDO()->prepare(
      Database::getOneQuery(self::$table, [ "code" ])
    );
    $statement->execute([$id]);
    $data = $statement->fetch(\PDO::FETCH_ASSOC);
    return EntityManager::hydrate(Equipment::class, $data);
  }

  public static function insert($data)
  {
    if ($data instanceof Equipment) {
      $statement = Database::getPDO()->prepare("call {$_SESSION['dataset']['id']}_sp_equipment_insert(:code, :designation, :categorie, :prix, :notes);");
      $statement->bindValue(':code', $data->getCode());
      $statement->bindValue(':designation', $data->getDesignation());
      $statement->bindValue(':categorie', $data->getCategorie());
      $statement->bindValue(':prix', $data->getPrix());
      $statement->bindValue(':notes', $data->getNotes());
      return $statement->execute();
    } else {
      $statement = Database::getPDO()->prepare(
        Database::insertQuery(
          self::$table,
          [
            "code",
            "designation",
            "categorie",
            "prix",
            "notes"
          ]
        )
      );
      return $statement->execute($data);
    }
  }

  public static function update($data)
  {
    if ($data instanceof Equipment) {
      $statement = Database::getPDO()->prepare("call {$_SESSION['dataset']['id']}_sp_equipment_update(:code, :designation, :categorie, :prix, :notes);");
      $statement->bindValue(':code', $data->getCode());
      $statement->bindValue(':designation', $data->getDesignation());
      $statement->bindValue(':categorie', $data->getCategorie());
      $statement->bindValue(':prix', $data->getPrix());
      $statement->bindValue(':notes', $data->getNotes());
      return $statement->execute();
    } else {
      $statement = Database::getPDO()->prepare(
        Database::updateQuery(
          self::$table,
          [
            "designation",
            "categorie",
            "prix",
            "notes"
          ],
          ["code"]
        )
      );
      return $statement->execute($data);
    }
  }

  public static function deleteOne($data)
  {
    if ($data instanceof Equipment) {
      $statement = Database::getPDO()->prepare("call {$_SESSION['dataset']['id']}_sp_equipment_delete(:code);");
      $statement->bindValue(':code', $data->getCode());
      return $statement->execute();
    } else {
      $statement = Database::getPDO()->prepare(
        Database::deleteOneQuery(
          self::$table,
          ["code"]
        )
      );
      return $statement->execute([$data]);
    }
  }

  public static function getProperties($id)
  {
    $sql = implode(
      " ",
      [
        "select *",
        "from " . Database::table("equipement_proprietes"),
        Database::buildWhere([ "code_equipement" ])
      ]
    );

    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([$id]);
    return $statement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function saveProperties($data)
  {
    $pdo = Database::getPDO();
    $pdo->beginTransaction();

    // remove existing
    $statement = $pdo->prepare(
      implode(" ",
      [
        "delete from",
        Database::table("equipement_proprietes"),
        Database::buildWhere([ "code_equipement" ])
      ]
      )
    );
    $statement->execute([ $data["code"] ]);

    // insert
    $statement = $pdo->prepare(
      Database::insertQuery(
        "equipement_proprietes",
        [
          "code_equipement",
          "code_propriete",
          "valeur"
        ]
      )
    );
    foreach ($data["props"] as $propKey => $propValue) {
      if ($propValue != null && $propValue != "") {
        $statement->execute(
          [
            "code_equipement" => $data["code"],
            "code_propriete" => $propKey,
            "valeur" => $propValue
          ]);
      }
    }

    $pdo->commit();
  }
}