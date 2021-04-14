<?php

namespace app\models;

use framework\Database;
use PDO;

class FamilyModel
{

  public static string $table = "familles";

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
      $family = $statement->fetch(PDO::FETCH_ASSOC);
    }
    return $family;
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
          "famille"
        ]
      )
    );
    return $statement->execute($data);
  }

  public static function deleteOne($id): bool
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