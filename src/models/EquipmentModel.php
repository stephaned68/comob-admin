<?php


namespace app\models;

use framework\Database;
use framework\QueryBuilder;
use PDO;

class EquipmentModel
{
  public static string $table = "equipement";

  public static function getAll(): array
  {
    $all = [];
    $pdo = Database::getPDO();
    if ($pdo) {
      $rs = $pdo->query(
        Database::getAllQuery(self::$table)
      );
      $all = $rs->fetchAll(PDO::FETCH_ASSOC);
    }
    return $all;
  }

  public static function getByCategory($category=null): array
  {
    $qb = new QueryBuilder();
    $qb
      ->from(Database::table(self::$table), "e")
      ->left(Database::table(CategoryModel::$table), "c.code", "e.categorie", "c")
      ->orderBy("c.parent")
      ->orderBy("c.sequence")
      ->orderBy("c.code")
      ->orderBy("e.sequence")
      ->orderBy("e.code")
      ->select([
        "e.code as code",
        "e.designation as designation",
        "c.libelle as categorie"
      ]);
    if ($category != null) {
      $qb->where("e.categorie = '$category'");
    }
    $sql = $qb->getQuery();

    $all = [];
    $pdo = Database::getPDO();
    if ($pdo) {
      $rs = Database::getPDO()->query($sql);
      $all = $rs->fetchAll(PDO::FETCH_ASSOC);
    }
    return $all;
  }

  public static function getByCategoryWithProps($category=null): array
  {

    $qb = new QueryBuilder();
    $qb
      ->from(Database::table(self::$table), "e")
      ->inner(Database::table(CategoryModel::$table), "c.code", "e.categorie", "c")
      ->left(Database::table("equipement_proprietes"), "ep.code_equipement", "e.code", "ep")
      ->left(Database::table(PropertyModel::$table), "pe.code", "ep.code_propriete", "pe")
      ->groupBy("c.code")
      ->groupBy("e.code")
      ->orderBy("c.parent")
      ->orderBy("c.sequence")
      ->orderBy("c.code")
      ->orderBy("e.sequence")
      ->orderBy("e.code")
      ->orderBy("pe.code")
      ->select([
        "e.code as code",
        "e.designation as designation",
        "c.libelle as categorie",
        "e.prix as prix",
        "group_concat(concat_ws(' : ', pe.intitule, ep.valeur) separator '\n ') as props"
      ])
      ;
    if ($category != null) {
      $qb->where("e.categorie = '$category'");
    }
    $sql = $qb->getQuery();

    $all = [];
    $pdo = Database::getPDO();
    if ($pdo) {
      $pdo->exec("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
      $rs = $pdo->query($sql);
      $all = $rs->fetchAll(PDO::FETCH_ASSOC);
    }
    return $all;
  }

  public static function getOne($id)
  {
    $equipment = [];
    $pdo = Database::getPDO();
    if ($pdo) {
      $statement = $pdo->prepare(
        Database::getOneQuery(
          self::$table,
          [
            "code"
          ]
        )
      );
      $statement->execute([ $id ]);
      $equipment = $statement->fetch(PDO::FETCH_ASSOC);
    }
    return $equipment;
  }

  public static function insert($data): bool
  {
    $statement = Database::getPDO()->prepare(
      Database::insertQuery(self::$table)
    );
    return $statement->execute($data);
  }

  public static function update($data): bool
  {
      $statement = Database::getPDO()->prepare(
        Database::updateQuery(
          self::$table,
          [
            "code"
          ]
        )
      );
      return $statement->execute($data);
  }

  public static function deleteOne($data): bool
  {
    $statement = Database::getPDO()->prepare(
      Database::deleteOneQuery(
        self::$table,
        [
          "code"
        ]
      )
    );
    return $statement->execute([$data]);
  }

  public static function getProperties($id): array
  {

    $qb = new QueryBuilder();
    $qb
      ->from(Database::table("equipement_proprietes"))
      ->where("code_equipement = ?")
      ->select();
    $sql = $qb->getQuery();

    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([ $id ]);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function saveProperties($data)
  {
    $pdo = Database::getPDO();
    $pdo->beginTransaction();

    // remove existing
    $sql = implode(" ",
      [
        "delete from",
        Database::table("equipement_proprietes"),
        Database::buildWhere([ "code_equipement" ])
      ]
    );
    $statement = $pdo->prepare($sql);
    $statement->execute([ $data["code"] ]);

    // insert
    $sql = Database::insertQuery(
      "equipement_proprietes",
      [
        "code_equipement",
        "code_propriete",
        "valeur"
      ]
    );
    $statement = $pdo->prepare($sql);
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