<?php


namespace m2i\project\models;

use m2i\framework\Database;

class ProfileModel
{
  public static $table = "profils";

  private static function getProfiles($family = "")
  {
    $sql =
      [
        "select",
        "pr.profil as pr_profil,",
        "pr.nom as pr_nom,",
        "fa.description as fa_description",
        "from " . Database::table(self::$table) . " as pr",
        "inner join " . Database::table("familles") . " as fa on pr.famille = fa.famille"
      ];
    $params = [];

    if ($family !== "") {
      array_push($sql, "where pr.famille = ?");
      array_push($params, $family);
    }

    array_push($sql, "order by pr.nom");
    $stmt = implode(" ", $sql);

    if ($family !== "") {
      $statement = Database::getPDO()->prepare($stmt);
      $statement->execute($params);
      $rs = $statement;
    } else {
      $rs = Database::getPDO()->query($stmt);
    }

    return $rs->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function getAll()
  {
    return self::getProfiles(); /*
    $sql = implode(" ",
      [
        "select",
        "pr.profil as pr_profil,",
        "pr.nom as pr_nom,",
        "case when pr.type = '0' then fa.description",
        "else 'Hybride' end as fa_description",
        "from " . Database::table(self::$table) . " as pr",
        "inner join " . Database::table("familles") . " as fa on pr.famille = fa.famille",
        "order by pr.nom"
      ]);
    $rs = Database::getPDO()->query($sql);
    return $rs->fetchAll(\PDO::FETCH_ASSOC); */
  }

  public static function getAllForFamily($family)
  {
    return self::getProfiles($family);
  }

  public static function getOne($id)
  {
    $sql = implode(" ",
      [
        "select * from",
        Database::table(self::$table),
        "where profil = ?"
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
        "(profil, nom, famille, type)",
        "values(:profil, :nom, :famille, :type)"
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
        "famille=:famille,",
        "type=:type",
        "where profil=:profil"
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
        "where profil = ?"
      ]);
    $statement = Database::getPDO()->prepare($sql);
    return $statement->execute([$id]);
  }

  public static function getPaths($id)
  {
    $sql = implode(" ",
      [
        "select * from",
        Database::table("voies_profils"),
        "where profil = ?"
      ]);
    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([$id]);
    return $statement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function savePaths($data)
  {
    $pdo = Database::getPDO();
    $pdo->beginTransaction();

    // remove existing
    $sql = implode(" ",
    [
      "delete from",
      Database::table("voies_profils"),
      "where profil = ? and voie = ?"
    ]);
    $statement = $pdo->prepare($sql);
    foreach ($data["voies"] as $voie) {
      $statement->execute(
        [
          $data["profil"],
          $voie
        ]);
    }

    // insert
    $sql = implode(" ",
      [
        "insert into",
        Database::table("voies_profils"),
        "(profil, voie)",
        "values(?, ?)"
      ]);
    $statement = $pdo->prepare($sql);
    foreach ($data["voies"] as $voie) {
      $statement->execute(
        [
          $data["profil"],
          $voie
        ]);
    }

    $pdo->commit();
  }
}