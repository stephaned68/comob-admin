<?php


namespace framework;


class Database
{

  /**
   * @var \PDO $pdo
   */
  private static $pdo = null;

  /**
   * Return a PDO connection
   * @return \PDO
   */
  public static function getPDO()
  {
    if (!self::$pdo) {
      self::$pdo = new \PDO(
        DSN,
        "root",
        "",
        [
          \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ]
      );
    }
    return self::$pdo;
  }

  /**
   * Return the table name for the current dataset
   * @param $table
   * @return string
   */
  public static function table(string $table)
  {
    return "{$_SESSION['dataset']['id']}_{$table}";
  }

  /**
   * Return a SQL where clause
   * @param array $filters
   * @param bool $ordinal
   * @param bool $orWhere
   * @return string
   */
  public static function buildWhere(array $filters, bool $ordinal = true, bool $orWhere = false)
  {
    $where = [];
    $andWhere = "where";

    foreach ($filters as $filter) {
      $where[] = "$andWhere $filter = " . (($ordinal) ? "?" : ":$filter");
      if ($andWhere === "where") {
        $andWhere = $orWhere ? "or" : "and";
      }
    }

    return implode(" ", $where);
  }

  /**
   * @param array $fields
   * @return string
   */
  public static function selectFields(array $fields = [])
  {
    if (count($fields) > 0) {
      return implode(", ", $fields);
    } else {
      return "*";
    }
  }

  /**
   * Return the SQL query to get all records
   * @param string $table
   * @param array $fields
   * @return string
   */
  public static function getAllQuery(string $table, array $fields = [])
  {
    $select = self::selectFields($fields);
    return implode(" ",
      [
        "select {$select}",
        "from " . self::table($table)
      ]);
  }

  /**
   * Return the SQL query to get one record
   * @param string $table
   * @param array $primaryKeys
   * @param array $fields
   * @return string
   */
  public static function getOneQuery(string $table, array $primaryKeys, array $fields = [])
  {
    $select = self::selectFields($fields);
    $sql =
      [
        "select {$select} from",
        self::table($table),
        self::buildWhere($primaryKeys)
      ];

    return implode(" ", $sql);
  }

  /**
   * Return the SQL query to insert a new record
   * @param string $table
   * @param array $fieldNames
   * @return string
   */
  public static function insertQuery(string $table, array $fieldNames)
  {
    $sql =
      [
        "insert into",
        Database::table($table),
        "(" . implode(", ", $fieldNames) . ")"
      ];

    $values = [];
    foreach ($fieldNames as $fieldName) {
      $values[] = ":$fieldName";
    }
    $sql[] = "values(" . implode(", ", $values) . ")";

    return implode(" ", $sql);
  }

  /**
   * Return the SQL query to update a record
   * @param string $table
   * @param array $fieldNames
   * @param array $primaryKeys
   * @return string
   */
  public static function updateQuery(string $table, array $fieldNames, array $primaryKeys)
  {
    $sql =
      [
        "update",
        self::table($table)
      ];

    $fields = [];
    foreach ($fieldNames as $fieldName) {
      $fields[] = "$fieldName = :$fieldName";
    }

    $sql[] = "set " . implode(", ", $fields);

    $sql[] = self::buildWhere($primaryKeys, false);

    return implode(" ", $sql);
  }

  /**
   * Return the SQL query to remove a record
   * @param string $table
   * @param array $primaryKeys
   * @return string
   */
  public static function deleteOneQuery(string $table, array $primaryKeys)
  {
    $sql =
      [
        "delete from",
        self::table($table),
        self::buildWhere($primaryKeys)
      ];

    return implode(" ", $sql);
  }

  /**
   * Persist a record
   * @param FormManager $form
   * @param string|null $id
   * @param string $model
   * @param array $messages
   * @return bool
   */
  public static function save(FormManager $form, $id, $model, array $messages): bool
  {
    $success = false;

    if ($form->isValid()) {
      $message = null;
      $data = $form->getData();
      if ($id) {
        try {
          $model::update($data);
          $message = $messages["update"];
        } catch (\PDOException $ex) {
          Tools::setFlash("Erreur SQL" . $ex->getMessage(), "error");
        }
      } else {
        try {
          $model::insert($data);
          $message = $messages["insert"];
        } catch (\PDOException $ex) {
          Tools::setFlash("Erreur SQL" . $ex->getMessage(), "error");
        }
      }
      if ($message) {
        Tools::setFlash($message);
      }
      $success = true;
    } else {
      $errors = $form->validateForm();
      Tools::setFlash($errors, "warning");
    }

    return $success;
  }

  /**
   * Delete a record
   * @param $id
   * @param $model
   * @param array $messages
   * @return bool
   */
  public static function remove($id, $model, array $messages)
  {
    if (!$id) {
      return false;
    }

    $data = null;
    try {
      $data = $model::getOne($id);
    } catch (\PDOException $ex) {
      Tools::setFlash("Erreur SQL" . $ex->getMessage(),"error");
    }

    if (!$data) {
      Tools::setFlash($messages["failure"], "warning");
    } else {
      try {
        $model::deleteOne($id);
        Tools::setFlash($messages["success"]);
      } catch (\PDOException $ex) {
        Tools::setFlash("Erreur SQL" . $ex->getMessage(),"error");
      }
    }

    return true;
  }

  public static function getTypes(string $table, string $valueField, string $labelField)
  {
    $rs = self::getPDO()->query(
      self::getAllQuery($table)
    );
    $types = Tools::select($rs->fetchAll(\PDO::FETCH_ASSOC),$valueField, $labelField);
    return array_merge(["" => "Base"], $types);
  }
}