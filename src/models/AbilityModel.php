<?php


namespace m2i\project\models;


use m2i\framework\Database;

class AbilityModel
{

  public static $table = "capacites";

  public static function getTypes()
  {
    return [
      "0" => "Profil",
      "1" => "Raciale",
      "2" => "Prestige",
      "3" => "Epique"
    ];
  }

  public static function getAll()
  {
    $sql = implode(" ",
      [
        "select *",
        "from " . Database::table(self::$table)
      ]);
    $rs = Database::getPDO()->query($sql);
    return $rs->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getAllForType($type)
  {
    $sql = implode(" ",
      [
        "select *",
        "from " . Database::table(self::$table),
        "where type = ?"
      ]);
    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([$type]);
    return $statement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getOne($id)
  {
    $sql = implode(" ",
      [
        "select * from",
        Database::table(self::$table),
        "where capacite = ?"
      ]);
    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([$id]);
    return $statement->fetch(\PDO::FETCH_ASSOC);
  }

  public static function insert($data)
  {
    $sql = implode(" ",
      [
        "insert into",
        Database::table(self::$table),
        "(capacite, nom, limitee, sort, type, description)",
        "values(:capacite, :nom, :limitee, :sort, :type, :description)"
      ]);
    $statement = Database::getPDO()->prepare($sql);
    return $statement->execute($data);
  }

  public static function update($data)
  {
    $sql = implode(" ",
      [
        "update",
        Database::table(self::$table),
        "set",
        "nom=:nom,",
        "limitee=:limitee,",
        "sort=:sort,",
        "type=:type,",
        "description=:description",
        "where capacite=:capacite"
      ]);
    $statement = Database::getPDO()->prepare($sql);
    return $statement->execute($data);
  }

  public static function deleteOne($id)
  {
    $sql = implode(" ",
      [
        "delete from",
        Database::table(self::$table),
        "where capacite = ?"
      ]);
    $statement = Database::getPDO()->prepare($sql);
    return $statement->execute([$id]);
  }

}