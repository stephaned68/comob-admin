<?php


namespace m2i\project\models;


use m2i\framework\Database;

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
    $rs = Database::getPDO()->query(
      Database::getAllQuery(self::$table)
    );
    return $rs->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getAllForType($type)
  {
    $sql = implode(" ",
      [
        Database::getAllQuery(self::$table),
        Database::buildWhere(["type"])
      ]);
    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([$type]);
    return $statement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getOne($id)
  {
    $statement = Database::getPDO()->prepare(
      Database::getOneQuery(self::$table, ["capacite"])
    );
    $statement->execute([$id]);
    return $statement->fetch(\PDO::FETCH_ASSOC);
  }

  public static function insert($data)
  {
    $statement = Database::getPDO()->prepare(
      Database::insertQuery(
        self::$table,
        ["capacite", "nom", "limitee", "sort", "type", "description"]
      )
    );
    return $statement->execute($data);
  }

  public static function update($data)
  {
    $statement = Database::getPDO()->prepare(
      Database::updateQuery(
        self::$table,
        ["nom", "limitee", "sort", "type", "description"],
        ["capacite"]
      )
    );
    return $statement->execute($data);
  }

  public static function deleteOne($id)
  {
    $statement = Database::getPDO()->prepare(
      Database::deleteOneQuery(
        self::$table,
        ["capacite"]
      )
    );
    return $statement->execute([$id]);
  }

}