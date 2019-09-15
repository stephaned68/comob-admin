<?php


namespace app\models;

use framework\Database;
use framework\Tools;

class PathModel
{
  public static $table = "voies";

  public static function getTypes()
  {
    return Database::getTypes(
      "types_voie",
      "type_voie",
      "type_voie_intitule"
    );
  }

  public static function getAll()
  {
    $rs = Database::getPDO()->query(
      Database::getAllQuery(self::$table)
    );
    return $rs->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getAllForType($type)
  {
    $sql = implode(" ",
      [
        Database::getAllQuery(self::$table),
        Database::buildWhere(["type"])
      ]);
    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([$type]);
    return $statement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getOne($id)
  {
    $statement = Database::getPDO()->prepare(
      Database::getOneQuery(self::$table, ["voie"])
    );
    $statement->execute([$id]);
    return $statement->fetch(\PDO::FETCH_ASSOC);
  }

  public static function insert($data)
  {
    $statement = Database::getPDO()->prepare(
      Database::insertQuery(
        self::$table,
        ["voie", "nom", "notes", "type", "pfx_deladu"]
      )
    );
    return $statement->execute($data);
  }

  public static function update($data)
  {
    $statement = Database::getPDO()->prepare(
      Database::updateQuery(
        self::$table,
        ["voie", "nom", "notes", "type", "pfx_deladu"],
        ["voie"]
      )
    );
    return $statement->execute($data);
  }

  public static function deleteOne($id)
  {
    $statement = Database::getPDO()->prepare(
      Database::deleteOneQuery(
        self::$table,
        ["voie"]
      )
    );
    return $statement->execute([$id]);
  }

  public static function getAbilities($id)
  {
    $sql = implode(" ",
      [
        Database::getAllQuery("capacites_voies"),
        Database::buildWhere(["voie"])
      ]);
    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([$id]);
    return $statement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function saveAbilities($data)
  {
    $pdo = Database::getPDO();
    $pdo->beginTransaction();

    // remove existing
    $statement = $pdo->prepare(
      Database::deleteOneQuery(
        "capacites_voies",
        [
          "voie",
          "rang"
        ]
      )
    );
    for ($rang = 0; $rang < 5; $rang++) {
      $statement->execute(
        [
          $data["voie"],
          $rang + 1
        ]);
    }

    // insert
    $statement = $pdo->prepare(
      Database::insertQuery(
        "capacites_voies",
        [
          "voie",
          "rang",
          "capacite"
        ]
      )
    );
    for ($rang = 0; $rang < 5; $rang++) {
      $statement->execute(
        [
          "voie" => $data["voie"],
          "rang" => $rang + 1,
          "capacite" => $data["capacites"][$rang]
        ]);
    }

    $pdo->commit();
  }
}