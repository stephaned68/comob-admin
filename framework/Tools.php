<?php


namespace m2i\framework;


class Tools
{

  /**
   * @param string $str
   * @return string|string[]|null
   */
  public static function pascalize($str)
  {
    $pattern = "#(\_|-| )?([a-zA-Z0-9])+#";
    return preg_replace_callback(
      $pattern,
      function ($matches) {
        $matches[0] = str_replace($matches[1], "", $matches[0]);
        $matches[0] = strtoupper(substr($matches[0], 0, 1))
          . strtolower(substr($matches[0], 1));

        return $matches[0];
      },
      $str
    );
  }

  /**
   * @param string $str
   * @return string
   */
  public static function camelize($str)
  {
    $temp = self::pascalize($str);
    return strtolower(substr($temp, 0, 1))
      . substr($temp, 1);
  }

  /**
   * @param string $message
   * @param null $type
   */
  public static function setFlash($message, $type = null)
  {
    $type = ($type) ? $type : "flash";

    if (array_key_exists($type, $_SESSION)) {
      $messages = $_SESSION[$type];
    } else {
      $messages = [];
    }

    if (is_array($message)) {
      if (is_array($messages) && count($messages) > 0) {
        array_merge($messages, $message);
      } else {
        $messages = $message;
      }
    } else {
      array_push($messages, $message);
    }
    $_SESSION[$type] = $messages;
  }

  /**
   * @param null $type
   * @return mixed|string
   */
  public static function getFlash($type = null)
  {
    $type = ($type) ? $type : "flash";
    $messages = $_SESSION[$type] ?? [];
    unset($_SESSION[$type]);
    return $messages;
  }

  /**
   * @param array $list
   * @param string $valueField
   * @param string $labelField
   * @return array
   */
  public static function select($list, $valueField, $labelField)
  {
    $select = [];
    foreach ($list as $item) {
      $select[$item[$valueField]] = $item[$labelField];
    }

    return $select;
  }

}