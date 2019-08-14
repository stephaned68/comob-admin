<?php


namespace m2i\project\models;

use m2i\framework\Database;

class PathModel
{
  public static $table = "voies";

  public static function getTypes()
  {
    return [
      "0" => "Profil",
      "1" => "Raciale",
      "2" => "Prestige"
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
        "where voie = ?"
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
        "(voie, nom, notes, type)",
        "values(:voie, :nom, :notes, :type)"
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
        "notes=:notes,",
        "type=:type",
        "where voie=:voie"
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
        "where voie = ?"
      ]);
    $statement = Database::getPDO()->prepare($sql);
    return $statement->execute([$id]);
  }

  public static function getAbilities($id)
  {
    $sql = implode(" ",
      [
        "select * from",
        Database::table("capacites_voies"),
        "where voie = ?"
      ]);
    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([$id]);
    return $statement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function saveAbilities($data)
  {
    $pdo = Database::getPDO();
    $pdo->beginTransaction();

    // remove existing
    $sql = implode(" ",
      [
        "delete from",
        Database::table("capacites_voies"),
        "where voie = ? and rang = ?"
      ]);
    $statement = $pdo->prepare($sql);
    for ($rang = 0; $rang < 5; $rang++) {
      $statement->execute(
        [
          $data["voie"],
          $rang + 1
        ]);
    }

    // insert
    $sql = implode(" ",
      [
        "insert into",
        Database::table("capacites_voies"),
        "(voie, rang, capacite)",
        "values(?, ?, ?)"
      ]);
    $statement = $pdo->prepare($sql);
    for ($rang = 0; $rang < 5; $rang++) {
      $statement->execute(
        [
          $data["voie"],
          $rang + 1,
          $data["capacites"][$rang]
        ]);
    }

    $pdo->commit();
  }
}