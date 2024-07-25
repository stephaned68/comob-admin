<?php


namespace app\models;

use framework\Database;
use PDO;
use PDOException;

class PathModel
{
  public static string $table = "voies";

  public static function getTypes(): array
  {
    return Database::getTypes(
      "types_voie",
      "type_voie",
      "type_voie_intitule"
    );
  }

  public static function getOneType($id) {
    $type = [];
    $pdo = Database::getPDO();
    if ($pdo) {
      $statement = $pdo->prepare(
        Database::getOneQuery(
          Database::table("types_voie"),
          [
            "type_voie"
          ]
        )
      );
      $statement->execute([$id]);
      $type = $statement->fetch(PDO::FETCH_ASSOC);
    }
    return $type;
  }

  public static function getAll()
  {
    $all = [];
    $pdo = Database::getPDO();
    if ($pdo) {
      $rs = $pdo->query(
        Database::getAllQuery(self::$table)
      );
      $all = $rs->fetchAll(PDO::FETCH_ASSOC);
    }
    return $all;
  }

  public static function getAllForType($type)
  {
    $qb = Database::getAll(self::$table);
    if ($type == "" || $type == null) {
      $qb->where("type = ''");
      $qb->orWhere("type is null");
    } else {
      $qb->where("type = ?");
    }

    $all = [];
    $pdo = Database::getPDO();
    if ($pdo) {
      $statement = $pdo->prepare($qb->getQuery());
      if ($type == "" || $type == null) {
        $statement->execute();
      } else {
        $statement->execute([$type]);
      }
      $all = $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    return $all;
  }

  public static function getAllButType($type): array
  {
    $qb = Database::getAll(self::$table);
    $qb->where("type <> ?");

    $all = [];
    $pdo = Database::getPDO();
    if ($pdo)
    {
      $statement = $pdo->prepare($qb->getQuery());
      $statement->execute([$type]);
      $all = $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    return $all;
  }

  public static function getOne($id)
  {
    $path = [];
    $pdo = Database::getPDO();
    if ($pdo) {
      $statement = $pdo->prepare(
        Database::getOneQuery(
          self::$table,
          [
            "voie"
          ]
        )
      );
      $statement->execute([ $id ]);
      $path = $statement->fetch(PDO::FETCH_ASSOC);
    }
    return $path;
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
          "voie"
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
          "voie"
        ]
      )
    );
    return $statement->execute([ $id ]);
  }

  public static function getAbilities($id): array
  {
    $sql = implode(" ",
      [
        Database::getAllQuery("capacites_voies"),
        Database::buildWhere(["voie"])
      ]);
    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([ $id ]);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function saveAbilities($data)
  {
    $pdo = Database::getPDO();
    $pdo->beginTransaction();
    $rangs = intval($data["rangs"] ?? "5");

    // remove existing
    $statement = $pdo->prepare(
      Database::deleteOneQuery(
        "capacites_voies",
        [
          "voie",
          "rang"
        ]
      )
    );
    for ($rang = 0; $rang < $rangs; $rang++) {
      try {
        $statement->execute(
          [
            $data["voie"],
            $rang + 1
          ]);
      } catch (PDOException $ex) {
        var_dump($rang);
      }
    }

    // insert
    $statement = $pdo->prepare(
      Database::insertQuery(
        "capacites_voies",
        [
          "voie",
          "rang",
          "capacite"
        ]
      )
    );
    for ($rang = 0; $rang < $rangs; $rang++) {
      $capacite = $data["capacites"][$rang];
      if ($capacite !== "") {
        $statement->execute(
          [
            "voie" => $data["voie"],
            "rang" => $rang + 1,
            "capacite" => $capacite
          ]);
      }
    }

    $pdo->commit();
  }
}