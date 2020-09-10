<?php

namespace app\models;

use framework\Database;

class FamilyModel
{

  public static $table = "familles";

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

  public static function getOne($id)
  {
    $family = [];
    $pdo = Database::getPDO();
    if ($pdo) {
      $statement = $pdo->prepare(
        Database::getOneQuery(
          self::$table,
          [
            "famille"
          ]
        )
      );
      $statement->execute([$id]);
      $family = $statement->fetch(\PDO::FETCH_ASSOC);
    }
    return $family;
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
          "famille"
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
          "famille"
        ]
      )
    );
    return $statement->execute([ $id ]);
  }

}