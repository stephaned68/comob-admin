<?php


namespace framework;


class Tools
{

  public static array $themeList =
    [
      "cerulean",
      "cosmo",
      "cyborg",
      "darkly",
      "flatly",
      "journal",
      "litera",
      "lumen",
      "lux",
      "materia",
      "minty",
      "pulse",
      "sandstone",
      "simplex",
      "sketchy",
      "slate",
      "solar",
      "spacelab",
      "superhero",
      "united",
      "yeti"
    ];

  public static function dump_var($var)
  {
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
  }

  /**
   * @param string $stripAccents
   * @return string
   */
  public static function stripAccents(string $stripAccents): string
  {
    return strtr(
      $stripAccents,
      'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
      'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
    );
  }

  /**
   * @param string $singular
   * @return string
   */
  public static function pluralize(string $singular): string
  {
    $last_letter = $singular[strlen($singular)-1];
    switch ($last_letter) {
      case 'y':
        return substr($singular,0, -1) . "ies";
      case 's':
        return $singular . "es";
      default:
        return $singular . "s";
    }
  }

  /**
   * @param string $str
   * @return string|string[]|null
   */
  public static function pascalize(string $str)
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
  public static function camelize(string $str): string
  {
    $temp = self::pascalize($str);
    return strtolower(substr($temp, 0, 1))
      . substr($temp, 1);
  }

  /**
   * @param string $str
   * @return string
   */
  public static function snakeCase(string $str): string
  {
    $temp = str_split($str);
    $result = "";
    foreach ($temp as $char)
    {
      if (strtoupper($char) == $char && $result != "") {
        $result .= "_";
      }
      $result .= strtolower($char);
    }
    return $result;
  }

  /**
   * @param string|array $message
   * @param null $type
   */
  public static function setFlash($message, $type = null)
  {
    $type = $type ?? "primary";

    if (array_key_exists("flash", $_SESSION)) {
      $messages = $_SESSION["flash"];
    } else {
      $messages = [];
    }

    if (is_array($message)) {
      foreach ($message as $mtype => $mtext) {
        if (array_key_exists($mtype, $messages)) {
          $messages[$mtype][] = $mtext;
        } else {
          $messages[$mtype] = [ $mtext ];
        }
      }
    } else {
      if (array_key_exists($type, $messages)) {
        $messages[$type][] = $message;
      } else {
        $messages[$type] = [ $message ];
      }
    }
    $_SESSION["flash"] = $messages;
  }

  /**
   * @return mixed|string
   */
  public static function getFlash()
  {
    $messages = $_SESSION["flash"] ?? [];
    unset($_SESSION["flash"]);
    return $messages;
  }

  /**
   * @param array $list
   * @param string $valueField
   * @param string $labelField
   * @param bool $showValue
   * @return array
   */
  public static function select(array $list, string $valueField, string $labelField, $showValue = false): array
  {
    $select = [];
    foreach ($list as $item) {
      $select[$item[$valueField]] = $item[$labelField] . ($showValue ? " (" . $item[$valueField] . ")" : "");
    }

    return $select;
  }

  /**
   * @param $list
   * @param $groupField
   * @param $valueField
   * @param $labelField
   * @return array
   */
  public static function selectGroup($list, $groupField, $valueField, $labelField): array
  {
    $groups = [];
    foreach ($list as $item) {
      if (!in_array($item[$groupField], $groups)) {
        $groups[$item[$groupField]] = [];
      }
    }
    foreach ($groups as $groupK => $groupV) {
      foreach ($list as $item) {
        if ($item[$groupField] === $groupK) {
          $groups[$groupK][$item[$valueField]] = $item[$labelField];
        }
      }
    }

    return $groups;
  }

  /**
   * Set default theme for session
   */
  public static function setTheme()
  {
    if (isset($_GET["theme"])) {
      $theme = $_GET["theme"];
      if ($theme === "!random") {
        unset($_SESSION["theme"]);
      } else {
        if (array_search($theme, self::$themeList)) {
          $_SESSION["theme"] = $theme;
        }
      }
    }
    if(!isset($_SESSION["theme"])) {
        $theme = rand(0, count(self::$themeList)-1);
        $_SESSION["theme"] = self::$themeList[$theme];
    }
  }

}