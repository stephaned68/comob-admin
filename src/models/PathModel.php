<?php


namespace app\models;

use framework\Database;
use PDOException;

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

  public static function getOneType($id) {
    $statement = Database::getPDO()->prepare(
      Database::getOneQuery(
        Database::table("types_voie"),
        [
          "type_voie"
        ]
      )
    );
    $statement->execute([ $id ]);
    return $statement->fetch(\PDO::FETCH_ASSOC);
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
    $qb = Database::getAll(self::$table);
    if ($type == "" || $type == null) {
      $qb->where("type = ''");
      $qb->orWhere("type is null");
    } else {
      $qb->where("type = ?");
    }
    $statement = Database::getPDO()->prepare($qb->getQuery());
    if ($type == "" || $type == null) {
      $statement->execute();
    } else {
      $statement->execute([ $type ]);
    }
    return $statement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getOne($id)
  {
    $statement = Database::getPDO()->prepare(
      Database::getOneQuery(
        self::$table,
        [
          "voie"
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
          "voie"
        ]
      )
    );
    return $statement->execute($data);
  }

  public static function deleteOne($id)
  {
    $statement = Database::getPDO()->prepare(
      Database::deleteOneQuery(
        self::$table,
        [
          "voie"
        ]
      )
    );
    return $statement->execute([ $id ]);
  }

  public static function getAbilities($id)
  {
    $sql = implode(" ",
      [
        Database::getAllQuery("capacites_voies"),
        Database::buildWhere(["voie"])
      ]);
    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([ $id ]);
    return $statement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function saveAbilities($data)
  {
    $pdo = Database::getPDO();
    $pdo->beginTransaction();
    $rangs = intval($data["rangs"] ?? "5");

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
    for ($rang = 0; $rang < $rangs; $rang++) {
      try {
        $statement->execute(
          [
            $data["voie"],
            $rang + 1
          ]);
      } catch (PDOException $ex) {
        var_dump($rang);
      }
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
    for ($rang = 0; $rang < $rangs; $rang++) {
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