<?php


namespace app\models;


class Property
{
  /**
   * @var ?string
   */
  private $code;

  /**
   * @var ?string
   */
  private $intitule;

  /**
   * @var ?string
   */
  private $defaut;

  /**
   * Property constructor.
   * @param $code
   * @param $intitule
   * @param $defaut
   */
  public function __construct($code = null, $intitule = null, $defaut = null)
  {
    $this->code = $code;
    $this->intitule = $intitule;
    $this->defaut = $defaut;
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
   * @return Property
   */
  public function setCode($code)
  {
    $this->code = $code;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getIntitule()
  {
    return $this->intitule;
  }

  /**
   * @param mixed $intitule
   * @return Property
   */
  public function setIntitule($intitule)
  {
    $this->intitule = $intitule;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getDefaut()
  {
    return $this->defaut;
  }

  /**
   * @param mixed $defaut
   * @return Property
   */
  public function setDefaut($defaut)
  {
    $this->defaut = $defaut;
    return $this;
  }

}