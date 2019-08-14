<?php


namespace m2i\framework;


class Database
{

  /**
   * Return a PDO connection
   * @return \PDO
   */
  public static function getPDO()
  {
    $options = [
      \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
    ];
    return new \PDO(
      DSN,
      "root",
      "",
      $options
    );
  }

  /**
   * Return the table name for the current dataset
   * @param $table
   * @return string
   */
  public static function table($table)
  {
    return "{$_SESSION['dataset']['id']}_{$table}";
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
          $success = true;
        }
      } else {
        try {
          $model::insert($data);
          $message = $messages["insert"];
        } catch (\PDOException $ex) {
          Tools::setFlash("Erreur SQL" . $ex->getMessage(), "error");
          $success = true;
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

}