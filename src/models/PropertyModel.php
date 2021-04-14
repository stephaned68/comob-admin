<?php


namespace app\models;

use framework\Database;
use framework\QueryBuilder;
use PDO;

class PropertyModel
{

  public static string $table = "proprietes_equipement";

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
    $property = [];
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
      $property = $statement->fetch(PDO::FETCH_ASSOC);
    }
    return $property;
  }

  public static function getByCategory($id): array
  {
    $qb = new QueryBuilder();
    $qb
      ->from(Database::table("categories_proprietes"), "cp")
      ->inner(Database::table(self::$table), "pe.code", "cp.code_propriete", "pe")
      ->where("cp.code_categorie = ?")
      ->select([
        "pe.code as code",
        "pe.intitule as intitule",
        "pe.defaut as defaut"
      ]);
    $sql = $qb->getQuery();

    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([ $id ]);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
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
    return $statement->execute([ $data ]);
  }

}