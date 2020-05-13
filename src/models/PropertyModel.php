<?php


namespace app\models;

use framework\Database;
use framework\QueryBuilder;

class PropertyModel
{

  public static $table = "proprietes_equipement";

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
          "code"
        ]
      )
    );
    $statement->execute([ $id ]);
    return $statement->fetch(\PDO::FETCH_ASSOC);
  }

  public static function getByCategory($id)
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
    return $statement->fetchAll(\PDO::FETCH_ASSOC);
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

}