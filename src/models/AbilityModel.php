<?php


namespace app\models;

use framework\Database;

class AbilityModel
{

  public static $table = "capacites";

  public static function getTypes()
  {
    return Database::getTypes(
      "types_capacite",
      "type_capacite",
      "type_capacite_intitule"
    );

  }

  public static function getAll()
  {
    $all = [];
    $pdo = Database::getPDO();
    if ($pdo) {
      $rs = $pdo->query(
        Database::getAllQuery(self::$table)
      );
      $all = $rs->fetchAll(\PDO::FETCH_ASSOC);
    }
    return $all;
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

    $all = [];
    $pdo = Database::getPDO();
    if ($pdo) {
      $statement = $pdo->prepare($qb->getQuery());
      if ($type == "" || $type == null) {
        $statement->execute();
      } else {
        $statement->execute([$type]);
      }
      $all = $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    return $all;
  }

  public static function getOne($id)
  {
    $ability = [];
    $pdo = Database::getPDO();
    if ($pdo) {
      $statement = $pdo->prepare(
        Database::getOneQuery(
          self::$table,
          [
            "capacite"
          ]
        )
      );
      $statement->execute([$id]);
      $ability = $statement->fetch(\PDO::FETCH_ASSOC);
    }
    return $ability;
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
          "capacite"
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
          "capacite"
        ]
      )
    );
    return $statement->execute([ $id ]);
  }

}