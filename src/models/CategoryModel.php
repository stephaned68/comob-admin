<?php


namespace app\models;

use framework\Database;
use framework\EntityManager;

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
    $sql = implode(
      " ",
      [
        "select *",
        "from " . Database::table(self::$table),
        "where parent is null or parent = ''"
      ]);

    $rs = Database::getPDO()->query("select * from {$_SESSION['dataset']['id']}_vu_category_getallmain");
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
        "from " . Database::table(self::$table) . " as c",
        "left join ". Database::table(self::$table) . " as p",
        "on c.parent = p.code",
        "order by c.parent, c.code"
      ]);

    $rs = Database::getPDO()->query("select * from {$_SESSION['dataset']['id']}_vu_category_getallwithmain");
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
        "where c.parent is not null",
        "and c.parent = p.code"
      ]);

    $rs = Database::getPDO()->query("select * from {$_SESSION['dataset']['id']}_vu_category_getallsubwithmain");
    return $rs->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getOne($id)
  {
    $statement = Database::getPDO()->prepare(
      Database::getOneQuery(self::$table, ["code"])
    );
    $statement->execute([$id]);
    $data =  $statement->fetch(\PDO::FETCH_ASSOC);
    return EntityManager::hydrate(Category::class, $data);
  }

  public static function insert($data)
  {
    if ($data instanceof Category) {
      $statement = Database::getPDO()->prepare("call {$_SESSION['dataset']['id']}_sp_category_insert(:code, :libelle, :parent);");
      $statement->bindValue(':code', $data->getCode());
      $statement->bindValue(':libelle', $data->getLibelle());
      $statement->bindValue(':parent', $data->getParent());
      return $statement->execute();
    } else {
      $statement = Database::getPDO()->prepare(
        Database::insertQuery(
          self::$table,
          ["code", "libelle", "parent"]
        )
      );
      return $statement->execute($data);
    }
  }

  public static function update($data)
  {
    if ($data instanceof Category) {
      $statement = Database::getPDO()->prepare("call {$_SESSION['dataset']['id']}_sp_category_update(:code, :libelle, :parent);");
      $statement->bindValue(':code', $data->getCode());
      $statement->bindValue(':libelle', $data->getLibelle());
      $statement->bindValue(':parent', $data->getParent());
      return $statement->execute();
    } else {
      $statement = Database::getPDO()->prepare(
        Database::updateQuery(
          self::$table,
          ["libelle", "parent"],
          ["code"]
        )
      );
      return $statement->execute($data);
    }
  }

  public static function deleteOne($data)
  {
    if ($data instanceof Category) {
      $statement = Database::getPDO()->prepare("call {$_SESSION['dataset']['id']}_sp_category_delete(:code);");
      $statement->bindValue(':code', $data->getCode());
      return $statement->execute();
    } else {
      $statement = Database::getPDO()->prepare(
        Database::deleteOneQuery(
          self::$table,
          ["code"]
        )
      );
      return $statement->execute([ $data ]);
    }
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

  public static function getPropertyList($id)
  {
    $sql = implode(" ",
      [
        "select",
        "cp.code_propriete as code,",
        "pr.intitule as intitule",
        "from",
        Database::table("categories_proprietes") . " as cp",
        "join " . Database::table("proprietes_equipement") . " as pr",
        "on pr.code = cp.code_propriete",
        Database::buildWhere([ "cp.code_categorie" ])
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