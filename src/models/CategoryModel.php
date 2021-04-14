<?php


namespace app\models;

use framework\Database;
use framework\QueryBuilder;
use PDO;

class CategoryModel
{

  public static string $table = "categories_equipement";

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

  public static function getAllMain(): array
  {
    $qb = new QueryBuilder(Database::table(self::$table));
    $qb
      ->where("parent is null")
      ->orWhere("parent = ''")
      ->select();

    $all = [];
    $pdo = Database::getPDO();
    if ($pdo) {
      $rs = $pdo->query($qb->getQuery());
      $all = $rs->fetchAll(PDO::FETCH_ASSOC);
    }

    return $all;
  }

  public static function getAllWithMain(): array
  {
    $qb = new QueryBuilder();
    $qb
      ->from(Database::table(self::$table), "c")
      ->left(Database::table(self::$table), "p.code", "c.parent", "p")
      ->orderBy("c.parent")
      ->orderBy("c.sequence")
      ->orderBy("c.code")
      ->select([
        "c.code as code",
        "c.libelle as libelle",
        "p.code as code_parent",
        "p.libelle as libelle_parent"
      ]);

    $all = [];
    $pdo = Database::getPDO();
    if ($pdo) {
      $rs = $pdo->query($qb->getQuery());
      $all = $rs->fetchAll(PDO::FETCH_ASSOC);
    }

    return $all;
  }

  public static function getAllSubWithMain(): array
  {
    $qb = new QueryBuilder();
    $qb
      ->from(Database::table(self::$table), "c")
      ->from(Database::table(self::$table), "p")
      ->where("c.parent is not null")
      ->andWhere("c.parent = p.code")
      ->orderBy("c.parent")
      ->orderBy("c.sequence")
      ->orderBy("c.code")
      ->select([
        "c.code as code",
        "c.libelle as libelle",
        "p.code as code_parent",
        "p.libelle as libelle_parent"
      ]);

    $all = [];
    $pdo = Database::getPDO();
    if ($pdo) {
      $rs = $pdo->query($qb->getQuery());
      $all = $rs->fetchAll(PDO::FETCH_ASSOC);
    }

    return $all;
  }

  public static function getOne($id)
  {
    $category = [];
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
      $statement->execute([$id]);
      $category = $statement->fetch(PDO::FETCH_ASSOC);
    }

    return $category;
  }

  public static function insert($data): bool
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

  public static function getProperties($id): array
  {
    $qb = new QueryBuilder();
    $qb
      ->from(Database::table("categories_proprietes"))
      ->where("code_categorie = ?")
      ->select();
    $sql = $qb->getQuery();

    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([ $id ]);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getPropertyList($id): array
  {
    $qb = new QueryBuilder();
    $qb
      ->from(Database::table("categories_proprietes"), "cp")
      ->inner(Database::table("proprietes_equipement"), "pr.code", "cp.code_propriete", "pr")
      ->where("cp.code_categorie = ?")
      ->select([
        "cp.code_propriete as code",
        "pr.intitule as intitule"
      ]);
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
    $statement = $pdo->prepare(
      Database::deleteOneQuery(
        "categories_proprietes",
        [
          "code_categorie"
        ]
      )
    );
    $statement->execute([ $data["code"] ]);

    // insert
    if (isset($data["properties"])) {
      $statement = $pdo->prepare(
        Database::insertQuery(
          "categories_proprietes",
          [
            "code_categorie",
            "code_propriete"
          ]
        )
      );
      foreach ($data["properties"] as $property) {
        $statement->execute(
          [
            "code_categorie" => $data["code"],
            "code_propriete" => $property
          ]);
      }
    }

    $pdo->commit();
  }

}