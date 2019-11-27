<?php


namespace app\models;


class Category
{

  /**
   * @var ?string
   */
  private $code;

  /**
   * @var ?string
   */
  private $libelle;

  /**
   * @var ?string
   */
  private $parent;

  /**
   * Category constructor.
   * @param $code
   * @param $libelle
   * @param $parent
   */
  public function __construct($code = null, $libelle = null, $parent = null)
  {
    $this->code = $code;
    $this->libelle = $libelle;
    $this->parent = $parent;
  }


  /**
   * @return mixed
   */
  public function getCode()
  {
    return $this->code;
  }

  /**
   * @param mixed $code
   * @return Category
   */
  public function setCode($code)
  {
    $this->code = $code;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getLibelle()
  {
    return $this->libelle;
  }

  /**
   * @param mixed $libelle
   * @return Category
   */
  public function setLibelle($libelle)
  {
    $this->libelle = $libelle;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getParent()
  {
    return $this->parent;
  }

  /**
   * @param mixed $parent
   * @return Category
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
    return $this;
  }

}