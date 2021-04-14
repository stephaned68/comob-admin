<?php


namespace app\models;

use framework\Database;
use framework\QueryBuilder;
use PDO;

class ProfileModel
{
  public static string $table = "profils";

  public static function getTypes(): array
  {
    return [
      "0" => "Base",
      "1" => "Hybride"
    ];
  }

  private static function getProfiles($family = ""): array
  {
    $qb = new QueryBuilder();
    $qb
      ->from(Database::table(self::$table), "pr")
      ->inner(Database::table(FamilyModel::$table), "fa.famille", "pr.famille", "fa")
      ->select([
        "pr.profil as profil",
        "pr.nom as nom",
        "fa.description as famille"
      ])
      ->orderBy("pr.nom")
    ;
    $params = [];
    if ($family !== "") {
      $qb->where("pr.famille = ?");
      $params[] = $family;
    }
    $sql = $qb->getQuery();

    $all = [];
    $pdo = Database::getPDO();
    if ($pdo) {
      if ($family !== "") {
        $statement = $pdo->prepare($sql);
        $statement->execute($params);
        $rs = $statement;
      } else {
        $rs = $pdo->query($sql);
      }

      $all = $rs->fetchAll(PDO::FETCH_ASSOC);
    }
    return $all;
  }

  public static function getAll(): array
  {
    return self::getProfiles();
  }

  public static function getAllForFamily($family): array
  {
    return self::getProfiles($family);
  }

  public static function getOne($id)
  {
    $profile = [];
    $pdo = Database::getPDO();
    if ($pdo) {
      $statement = $pdo->prepare(
        Database::getOneQuery(
          self::$table,
          [
            "profil"
          ]
        )
      );
      $statement->execute([ $id ]);
      $profile = $statement->fetch(PDO::FETCH_ASSOC);
    }
    return $profile;
  }

  public static function insert($data): bool
  {
    $statement = Database::getPDO()->prepare(
      Database::insertQuery(self::$table)
    );
    return $statement->execute($data);
  }

  public static function update($data): bool
  {
    $statement = Database::getPDO()->prepare(
      Database::updateQuery(
        self::$table,
        [
          "profil"
        ]
      )
    );
    return $statement->execute($data);
  }

  public static function deleteOne($id): bool
  {
    $statement = Database::getPDO()->prepare(
      Database::deleteOneQuery(
        self::$table,
        [
          "profil"
        ]
      )
    );
    return $statement->execute([ $id ]);
  }

  public static function getPaths($id): array
  {
    $qb = new QueryBuilder();
    $qb
      ->from(Database::table("voies_profils"))
      ->where("profil = ?")
      ->select()
    ;
    $sql = $qb->getQuery();

    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([ $id ]);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function savePaths($data)
  {
    $pdo = Database::getPDO();
    $pdo->beginTransaction();

    // remove existing
    $statement = $pdo->prepare(
      Database::deleteOneQuery(
        "voies_profils",
        [ "profil" ]
      )
    );
    $statement->execute([ $data["profil"] ]);

    // insert
    if ($data["voies"] !== null) {
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
    }

    $pdo->commit();
  }

  public static function getEquipments($id): array
  {
    $qb = new QueryBuilder();
    $qb
      ->from(Database::table("equipement_profils"), "ep")
      ->inner(Database::table(EquipmentModel::$table), "eq.code", "ep.equipement", "eq")
      ->where("ep.profil = ?")
      ->select([
        "eq.code as code",
        "eq.designation as designation",
        "ep.nombre as nombre",
        "ep.special as special"
      ])
    ;
    $sql = $qb->getQuery();

    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([ $id ]);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function saveEquipments($data)
  {
    $pdo = Database::getPDO();
    $pdo->beginTransaction();

    // remove existing
    $sql = implode(" ",
      [
        "delete from",
        Database::table("equipement_profils"),
        Database::buildWhere([ "profil" ])
      ]
    );
    $statement = $pdo->prepare($sql);
    $statement->execute([ $data["profil"] ]);

    // insert
    $sql = Database::insertQuery(
      "equipement_profils",
      [
        "profil",
        "sequence",
        "equipement",
        "nombre",
        "special"
      ]
    );
    $statement = $pdo->prepare($sql);

    foreach ($data["equipments"] as $e => $equipment) {
      $number = $data["numbers"][intval($e)];
      if (intval($number) < 1)
        continue;
      $special = $data["specials"][intval($e)];
      $statement->execute(
        [
          "profil" => $data["profil"],
          "sequence" => 1 + intval($e),
          "equipement" => $equipment,
          "nombre" => $number,
          "special" => $special
        ]);
    }

    $pdo->commit();
  }

  public static function getTraits($id): array
  {
    $qb = new QueryBuilder();
    $qb
      ->from(Database::table("profils_traits"), "pt")
      ->where("pt.profil = ?")
      ->select([
        "pt.intitule as intitule",
        "pt.description as description"
      ])
    ;
    $sql = $qb->getQuery();

    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([ $id ]);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function saveTraits($data)
  {
    $pdo = Database::getPDO();
    $pdo->beginTransaction();

    // remove existing
    $sql = implode(" ",
      [
        "delete from",
        Database::table("profils_traits"),
        Database::buildWhere([ "profil" ])
      ]
    );
    $statement = $pdo->prepare($sql);
    $statement->execute([ $data["profil"] ]);

    // insert
    $sql = Database::insertQuery(
      "profils_traits",
      [
        "profil",
        "sequence",
        "intitule",
        "description"
      ]
    );
    $statement = $pdo->prepare($sql);

    foreach ($data["labels"] as $t => $intitule) {
      $description = $data["descriptions"][intval($t)];
      if ($intitule == "" || $description == "") {
        continue;
      }
      $statement->execute(
        [
          "profil" => $data["profil"],
          "sequence" => 1 + intval($t),
          "intitule" => $intitule,
          "description" => $description
        ]);
    }

    $pdo->commit();
  }
}