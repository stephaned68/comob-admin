<?php


namespace framework;

use PDO;
use PDOException;

class Database
{

  /**
   * @var ?PDO $pdo
   */
  private static ?PDO $pdo = null;

  /**
   * Return a PDO connection
   * @return PDO|null
   */
  public static function getPDO(): ?PDO
  {
    if (!self::$pdo) {
      try {
        self::$pdo = new PDO(
          DSN,
          DBUSER,
          DBPASS,
          [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
          ]
        );
        self::$pdo->exec("SET NAMES 'UTF8'");
      } catch (PDOException $ex) {
        Tools::setFlash($ex->getMessage(), "danger");
      }
    }
    return self::$pdo;
  }

  /**
   * Return the table name for the current dataset
   * @param string $table
   * @return string
   */
  public static function table(string $table): string
  {
    $dataset = $_SESSION['dataset']['id'] . "_";
    if (!str_starts_with($table, "co") && !str_starts_with($table, $dataset)) {
      return $dataset . $table;
    } else {
      return $table;
    }
  }

  /**
   * Check if a table or view exists in the database
   * @param string $table
   * @return bool
   */
  public static function exists(string $table): bool
  {
    $result = self::raw(implode(" ", [
      "SHOW TABLES",
      "FROM `" . DBNAME . "`",
      "WHERE `Tables_in_" . DBNAME . "`='$table'"
    ]))[0];
    return ($result["Tables_in_" . DBNAME] == $table);
  }

  /**
   * Return the list of columns for a table
   * @param string $table
   * @return array
   */
  public static function getColumnsList(string $table, string $data_type = ""): array
  {
    $qb = new QueryBuilder();
    $qb
      ->from("information_schema.columns")
      ->select(["column_name"])
      ->where("table_schema = '" . DBNAME . "'")
      ->andWhere("table_name = '" . self::table($table) . "'");
    if ($data_type != "") {
      $qb->andWhere("data_type LIKE '%$data_type%'");
    }

    $columns = [];
    $pdo = Database::getPDO();
    if ($pdo) {
      try {
        $statement = $pdo->query($qb->getQuery());
        $columns = $statement->fetchAll(PDO::FETCH_ASSOC);
      } catch (PDOException $ex) {
      }
    }
    unset($qb);

    $columnsList = [];
    foreach ($columns as $column) {
      $columnsList[] = $column["COLUMN_NAME"];
    }

    return $columnsList;
  }

  /**
   * Return a SQL where clause
   * @param array $filters
   * @param bool $ordinal
   * @param bool $orWhere
   * @return string
   */
  public static function buildWhere(array $filters, bool $ordinal = true, bool $orWhere = false): string
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
  public static function selectFields(array $fields = []): string
  {
    if (count($fields) > 0) {
      return implode(", ", $fields);
    } else {
      return "*";
    }
  }

  /**
   * Return QueryBuilder object to get all records
   * @param string $table
   * @param array $fields
   * @return QueryBuilder
   */
  public static function getAll(string $table, array $fields = []): QueryBuilder
  {
    $qb = new QueryBuilder(self::table($table));
    $qb->select($fields);
    return $qb;
  }

  /**
   * Return the SQL query to get all records
   * @param string $table
   * @param array $fields
   * @return string
   */
  public static function getAllQuery(string $table, array $fields = []): string
  {
    $qb = self::getAll($table, $fields);
    $query = $qb->getQuery();
    unset($qb);
    return $query;
  }

  /**
   * Return QueryBuilder object to get one record
   * @param string $table
   * @param array $primaryKeys
   * @param array $fields
   * @return QueryBuilder
   */
  public static function getOne(string $table, array $primaryKeys = ["id"], array $fields = []): QueryBuilder
  {
    $qb = new QueryBuilder(self::table($table));
    $qb->select($fields);
    $qb->where("{$primaryKeys[0]} = ?");
    if (count($primaryKeys) > 1) {
      foreach ($primaryKeys as $pk) {
        $qb->andWhere("{$pk} = ?");
      }
    }
    return $qb;
  }

  /**
   * Return the SQL query to get one record
   * @param string $table
   * @param array $primaryKeys
   * @param array $fields
   * @return string
   */
  public static function getOneQuery(string $table, array $primaryKeys = ["id"], array $fields = []): string
  {
    $qb = self::getOne($table, $primaryKeys, $fields);
    $query = $qb->getQuery();
    unset($qb);
    return $query;
  }

  /**
   * Return the SQL query to insert a new record
   * @param string $table
   * @param array $fieldNames
   * @return string
   */
  public static function insertQuery(string $table, array $fieldNames = []): string
  {
    if (count($fieldNames) == 0) {
      $fieldNames = self::getColumnsList(self::table($table));
    }
    $sql =
      [
        "insert into",
        self::table($table),
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
   * @param array $primaryKeys
   * @param array $fieldNames
   * @return string
   */
  public static function updateQuery(string $table, array $primaryKeys, array $fieldNames = []): string
  {
    if (count($fieldNames) == 0) {
      $fieldList = self::getColumnsList($table);
      foreach ($fieldList as $field) {
        if (!in_array($field, $primaryKeys)) {
          $fieldNames[] = $field;
        }
      }
    }
    $sql = [
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
  public static function deleteOneQuery(string $table, array $primaryKeys): string
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
  public static function save(FormManager $form, ?string $id, $model, array $messages): bool
  {
    $success = false;

    if ($form->isValid()) {
      $message = null;
      $data = $form->getData();
      try {
        if ($id) {
          $model::update($data);
          $message = $messages["update"];
        } else {
          $model::insert($data);
          $message = $messages["insert"];
        }
        $success = true;
      } catch (\PDOException $ex) {
          Tools::setFlash("Erreur SQL" . $ex->getMessage(), "danger");
      }
      if ($message) {
        Tools::setFlash($message, "success");
      }
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
  public static function remove($id, $model, array $messages): bool
  {
    if (!$id) {
      return false;
    }

    $success = false;

    $data = null;
    try {
      $data = $model::getOne($id);
    } catch (\PDOException $ex) {
      Tools::setFlash("Erreur SQL " . $ex->getMessage(), "danger");
    }

    if (!$data) {
      Tools::setFlash($messages["failure"], "danger");
    } else {
      try {
        $model::deleteOne($id);
        Tools::setFlash($messages["success"], "success");
        $success = true;
      } catch (\PDOException $ex) {
        if ($ex->errorInfo[0] == "23000") {
          Tools::setFlash($messages["integrity"] ?? "Erreur d'intégrité référentielle" . "\n" . $ex->getMessage(), "warning");
        } else {
          Tools::setFlash("Erreur SQL " . $ex->getMessage(), "danger");
        }
      }
    }

    return $success;
  }

  /**
   * Return a list of types for a select dropdown
   * @param string $table
   * @param string $valueField
   * @param string $labelField
   * @return array
   */
  public static function getTypes(string $table, string $valueField, string $labelField): array
  {
    $rs = self::getPDO()->query(
      self::getAllQuery($table)
    );
    $types = Tools::select($rs->fetchAll(PDO::FETCH_ASSOC), $valueField, $labelField);

    return array_merge(["" => "Base"], $types);
  }

  public static function raw(string $sqlQuery, array $values = []): array
  {
    $statement = self::getPDO()->prepare($sqlQuery);
    $statement->execute($values);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }
}
