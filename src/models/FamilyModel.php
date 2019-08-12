<?php

namespace m2i\project\models;

use m2i\framework\Database;

class FamilyModel
{

  public static $table = "familles";

  public static function getAll()
  {
    $sql = implode(" ",
      [
        "select * from",
        Database::table(self::$table)
      ]);
    $rs = Database::getPDO()->query($sql);
    return $rs->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getOne($id)
  {
    $sql = implode(" ",
      [
        "select * from",
        Database::table(self::$table),
        "where famille = ?"
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
        "(famille, description)",
        "values(:famille, :description)"
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
        "set description=:description",
        "where famille=:famille"
      ]);
    $statement = Database::getPDO()->prepare($sql);
    return $statement->execute($data);
  }

  public static function deleteOne($id)
  {
    $sql=implode(" ",
      [
        "delete from",
        Database::table(self::$table),
        "where famille = ?"
      ]);
    $statement = Database::getPDO()->prepare($sql);
    return $statement->execute([$id]);
  }

}