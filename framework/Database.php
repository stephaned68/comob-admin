<?php


namespace m2i\framework;


class Database
{

  /**
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
   * @param $table
   * @return string
   */
  public static function table($table)
  {
    return "{$_SESSION['dataset']['id']}_{$table}";
  }

}