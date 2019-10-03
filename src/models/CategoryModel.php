<?php


namespace app\models;

use framework\Database;

class CategoryModel
{

  private static $table = "categories_equipement";

  public static function getAll()
  {
    $rs = Database::getPDO()->query(
      Database::getAllQuery(self::$table)
    );
    return $rs->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getAllMain()
  {
    $sql = implode(
      " ",
      [
        "select *",
        "from " . Database::table(CategoryModel::$table),
        "where parent is null or parent = ''"
      ]);
    $rs = Database::getPDO()->query($sql);
    return $rs->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getAllWithMain()
  {

    $sql = implode(
      " ",
      [
        "select",
        "c.code as code,",
        "c.libelle as libelle,",
        "p.code as code_parent,",
        "p.libelle as libelle_parent",
        "from " . Database::table(CategoryModel::$table) . " as c",
        "left join ". Database::table(CategoryModel::$table) . " as p",
        "on c.parent = p.code",
        "order by c.parent, c.code"
      ]);

    $rs = Database::getPDO()->query($sql);
    return $rs->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getAllSubWithMain()
  {
    $sql = implode(
      " ",
      [
        "select",
        "c.code as code,",
        "c.libelle as libelle,",
        "p.code as code_parent,",
        "p.libelle as libelle_parent",
        "from",
        Database::table(CategoryModel::$table) . " as c,",
        Database::table(CategoryModel::$table) . " as p",
        "where c.parent is not null and c.parent = p.code"
      ]);
    $rs = Database::getPDO()->query($sql);
    return $rs->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getOne($id)
  {
    $statement = Database::getPDO()->prepare(
      Database::getOneQuery(self::$table, ["code"])
    );
    $statement->execute([$id]);
    return $statement->fetch(\PDO::FETCH_ASSOC);
  }

  public static function insert($data)
  {
    $statement = Database::getPDO()->prepare(
      Database::insertQuery(
        self::$table,
        [ "code", "libelle", "parent" ]
      )
    );
    return $statement->execute($data);
  }

  public static function update($data)
  {
    $statement = Database::getPDO()->prepare(
      Database::updateQuery(
        self::$table,
        [ "libelle", "parent" ],
        [ "code" ]
      )
    );
    return $statement->execute($data);
  }


  public static function deleteOne($id)
  {
    $statement = Database::getPDO()->prepare(
      Database::deleteOneQuery(
        self::$table,
        [ "code" ]
      )
    );
    return $statement->execute([$id]);
  }

  public static function getProperties($id)
  {
    $sql = implode(" ",
      [
        Database::getAllQuery("categories_proprietes"),
        Database::buildWhere(["code_categorie"])
      ]);
    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([$id]);
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

    $pdo->commit();
  }

}