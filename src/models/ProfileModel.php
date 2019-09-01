<?php


namespace m2i\project\models;

use m2i\framework\Database;

class ProfileModel
{
  public static $table = "profils";

  public static function getTypes()
  {
    return [
      "0" => "Base",
      "1" => "Hybride"
    ];
  }

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
      $sql[] = "where pr.famille = ?";
      $params[] = $family;
    }

    $sql[] = "order by pr.nom";

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
    return self::getProfiles();
  }

  public static function getAllForFamily($family)
  {
    return self::getProfiles($family);
  }

  public static function getOne($id)
  {
    $statement = Database::getPDO()->prepare(
      Database::getOneQuery(self::$table, ["profil"])
    );
    $statement->execute([$id]);
    return $statement->fetch(\PDO::FETCH_ASSOC);
  }

  public static function insert($data)
  {
    $statement = Database::getPDO()->prepare(
      Database::insertQuery(
        self::$table,
        ["profil", "nom", "famille", "type"]
      )
    );
    return $statement->execute($data);
  }

  public static function update($data)
  {
    $statement = Database::getPDO()->prepare(
      Database::updateQuery(
        self::$table,
        ["nom", "famille", "type"],
        ["profil"]
      )
    );
    return $statement->execute($data);
  }

  public static function deleteOne($id)
  {
    $statement = Database::getPDO()->prepare(
      Database::deleteOneQuery(
        self::$table,
        ["profil"]
      )
    );
    return $statement->execute([$id]);
  }

  public static function getPaths($id)
  {
    $sql = implode(" ",
      [
        Database::getAllQuery("voies_profils"),
        Database::buildWhere(["profil"])
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
    $statement = $pdo->prepare(
      Database::deleteOneQuery(
        "voies_profils",
        [
          "profil",
          "voie"
        ]
      )
    );
    foreach ($data["voies"] as $voie) {
      $statement->execute(
        [
          $data["profil"],
          $voie
        ]);
    }

    // insert
    $statement = $pdo->prepare(
      Database::insertQuery(
        "voies_profils",
        [
          "profil",
          "voie"
        ]
      )
    );
    foreach ($data["voies"] as $voie) {
      $statement->execute(
        [
          "profil" => $data["profil"],
          "voie" => $voie
        ]);
    }

    $pdo->commit();
  }
}