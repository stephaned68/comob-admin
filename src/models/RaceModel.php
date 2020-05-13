<?php

namespace app\models;

use framework\Database;
use framework\QueryBuilder;

class RaceModel
{

  public static $table = "races";

  public static function getTypes()
  {
    return Database::getTypes(
      "types_races",
      "type_race",
      "type_race_intitule"
    );

  }

  public static function getAll()
  {
    $rs = Database::getPDO()->query(
      Database::getAllQuery(self::$table)
    );
    return $rs->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getOne($id)
  {
    $statement = Database::getPDO()->prepare(
      Database::getOneQuery(
        self::$table,
        [
          "race"
        ]
      )
    );
    $statement->execute([ $id ]);
    return $statement->fetch(\PDO::FETCH_ASSOC);
  }

  public static function insert($data)
  {
    $statement = Database::getPDO()->prepare(
      Database::insertQuery(self::$table)
    );
    return $statement->execute($data);
  }

  public static function update($data)
  {
    $statement = Database::getPDO()->prepare(
      Database::updateQuery(
        self::$table,
        [
          "race"
        ]
      )
    );
    return $statement->execute($data);
  }

  public static function deleteOne($data)
  {
    $statement = Database::getPDO()->prepare(
      Database::deleteOneQuery(
        self::$table,
        [
          "race"
        ]
      )
    );
    return $statement->execute([ $data ]);
  }

  public static function getTraits($id)
  {
    $qb = new QueryBuilder();
    $qb
      ->from(Database::table("races_traits"), "rt")
      ->where("rt.race = ?")
      ->select([
        "rt.intitule as intitule",
        "rt.description as description"
      ])
    ;
    $sql = $qb->getQuery();

    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([ $id ]);
    return $statement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function saveTraits($data)
  {
    $pdo = Database::getPDO();
    $pdo->beginTransaction();

    // remove existing
    $sql = implode(" ",
      [
        "delete from",
        Database::table("races_traits"),
        Database::buildWhere([ "race" ])
      ]
    );
    $statement = $pdo->prepare($sql);
    $statement->execute([ $data["race"] ]);

    // insert
    $sql = Database::insertQuery(
      "races_traits",
      [
        "race",
        "sequence",
        "intitule",
        "description"
      ]
    );
    $statement = $pdo->prepare($sql);

    foreach ($data["labels"] as $t => $intitule) {
      $description = $data["descriptions"][intval($t)];
      if ($intitule == "" || $description == "") {
        continue;
      }
      $statement->execute(
        [
          "race" => $data["race"],
          "sequence" => 1 + intval($t),
          "intitule" => $intitule,
          "description" => $description
        ]);
    }

    $pdo->commit();
  }

  public static function getAbilities($id)
  {
    $qb = new QueryBuilder();
    $qb
      ->from(Database::table("races_capacites"))
      ->where("race = ?")
      ->select()
    ;
    $sql = $qb->getQuery();

    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([ $id ]);
    return $statement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function saveAbilities($data)
  {
    $pdo = Database::getPDO();
    $pdo->beginTransaction();

    // remove existing
    $sql = implode(" ",
      [
        "delete from",
        Database::table("races_capacites"),
        Database::buildWhere([ "race" ])
      ]
    );
    $statement = $pdo->prepare($sql);
    $statement->execute([ $data["race"] ]);

    // insert
    $sql = Database::insertQuery(
      "races_capacites",
      [
        "race",
        "capacite"
      ]
    );
    $statement = $pdo->prepare($sql);

    foreach ($data["abilities"] as $ability) {
      $statement->execute(
        [
          "race" => $data["race"],
          "capacite" => $ability
        ]);
    }

    $pdo->commit();
  }

}