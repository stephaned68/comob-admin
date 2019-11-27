<?php


namespace app\models;

use framework\Database;
use framework\EntityManager;

class PropertyModel
{

  private static $table = "proprietes_equipement";

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
      Database::getOneQuery(self::$table, ["code"])
    );
    $statement->execute([$id]);
    $data = $statement->fetch(\PDO::FETCH_ASSOC);
    return EntityManager::hydrate(Property::class, $data);
  }

  public static function getByCategory($id)
  {
    $sql = implode(
      " ",
      [
        "select",
        "pe.code as code,",
        "pe.intitule as intitule,",
        "pe.defaut as defaut",
        "from " . Database::table("categories_proprietes") . " as cp",
        "join ". Database::table(self::$table) . " as pe",
        "on cp.code_propriete = pe.code",
        Database::buildWhere([ "cp.code_categorie" ])
      ]
    );

    $statement = Database::getPDO()->prepare($sql);
    $statement->execute([$id]);
    return $statement->fetchAll(\PDO::FETCH_ASSOC);
  }

  public static function insert($data)
  {
    if ($data instanceof Property) {
      $statement = Database::getPDO()->prepare("call {$_SESSION['dataset']['id']}_sp_property_insert(:code, :intitule, :defaut);");
      $statement->bindValue(':code', $data->getCode());
      $statement->bindValue(':intitule', $data->getIntitule());
      $statement->bindValue(':defaut', $data->getDefaut());
      return $statement->execute();
    } else {
      $statement = Database::getPDO()->prepare(
        Database::insertQuery(
          self::$table,
          ["code", "intitule", "defaut"]
        )
      );
      return $statement->execute($data);
    }
  }

  public static function update($data)
  {
    if ($data instanceof Property) {
      $statement = Database::getPDO()->prepare("call {$_SESSION['dataset']['id']}_sp_property_update(:code, :intitule, :defaut);");
      $statement->bindValue(':code', $data->getCode());
      $statement->bindValue(':intitule', $data->getIntitule());
      $statement->bindValue(':defaut', $data->getDefaut());
      return $statement->execute();
    } else {
      $statement = Database::getPDO()->prepare(
        Database::updateQuery(
          self::$table,
          ["intitule", "defaut"],
          ["code"]
        )
      );
      return $statement->execute($data);
    }
  }


  public static function deleteOne($data)
  {
    if ($data instanceof Property) {
      $statement = Database::getPDO()->prepare("call {$_SESSION['dataset']['id']}_sp_property_delete(:code);");
      $statement->bindParam(':code', $data->getCode());
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

}