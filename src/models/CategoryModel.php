<?php


namespace app\models;

use framework\Database;
use framework\QueryBuilder;

class CategoryModel
{

  public static $table = "categories_equipement";

  public static function getAll()
  {
    $rs = Database::getPDO()->query(
      Database::getAllQuery(self::$table)
    );
    return $rs->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getAllMain()
  {
    $qb = new QueryBuilder(Database::table(self::$table));
    $qb
      ->where("parent is null")
      ->orWhere("parent = ''")
      ->select();

    // "select * from {$_SESSION['dataset']['id']}_vu_category_getallmain"
    $rs = Database::getPDO()->query($qb->getQuery());
    return $rs->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getAllWithMain()
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

    // "select * from {$_SESSION['dataset']['id']}_vu_category_getallwithmain"
    $rs = Database::getPDO()->query($qb->getQuery());
    return $rs->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getAllSubWithMain()
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

    // "select * from {$_SESSION['dataset']['id']}_vu_category_getallsubwithmain"
    $rs = Database::getPDO()->query($qb->getQuery());
    return $rs->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getOne($id)
  {
    $statement = Database::getPDO()->prepare(
      Database::getOneQuery(
        self::$table,
        [
          "code"
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
          "code"
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
          "code"
        ]
      )
    );
    return $statement->execute([ $data ]);
  }

  public static function getProperties($id)
  {
    $qb = new QueryBuilder();
    $qb
      ->from(Database::table("categories_proprietes"))
      ->where("code_categorie = ?")
      ->select();
    $sql = $qb->getQuery();

    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([ $id ]);
    return $statement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getPropertyList($id)
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
    return $statement->fetchAll(\PDO::FETCH_ASSOC);
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