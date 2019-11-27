<?php


namespace framework;


class EntityManager
{

  public static function isEmpty($entity)
  {
    $empty = true;

    foreach (get_class_methods($entity) as $method) {
      if (substr($method, 0, 3) === "get") {
        if ($entity->$method()) {
          $empty = false;
          break;
        }
      }
    }

    return $empty;
  }

  public static function setProperty($entity, $fieldName, $fieldValue)
  {
    $setter = "set" . Tools::pascalize($fieldName);
    if (method_exists($entity, $setter)) {
      $entity->$setter($fieldValue);
    }
  }

  public static function hydrate($className, $data)
  {
    $entity = null;

    if ($className != "") {
      $entity = new $className();
      foreach ($data as $fieldName => $fieldValue) {
        EntityManager::setProperty($entity, $fieldName, $fieldValue);
      }
    }

    return $entity;
  }

  public static function getValue($column, $entity)
  {
    $getter = "get" . Tools::pascalize($column);
    if (method_exists($entity, $getter)) {
      return $entity->$getter();
    } else {
      return null;
    }
  }

  public static function getValues($columns, $entity) : array
  {
    $data = [];

    foreach($columns as $column) {
      $data[$column] = EntityManager::getValue($column, $entity);
    }

    return $data;
  }

}