<?php


namespace app\models;


class Equipment
{

  /**
   * @var ?string
   */
  private $code;

  /**
   * @var ?string
   */
  private $designation;

  /**
   * @var ?string
   */
  private $categorie;

  /**
   * @var ?integer
   */
  private $prix;

  /**
   * @var ?string
   */
  private $notes;

  /**
   * Equipment constructor.
   * @param $code
   * @param $designation
   * @param $categorie
   * @param $prix
   * @param $notes
   */
  public function __construct($code = null, $designation = null, $categorie = null, $prix = null, $notes = null)
  {
    $this->code = $code;
    $this->designation = $designation;
    $this->categorie = $categorie;
    $this->prix = $prix;
    $this->notes = $notes;
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
   * @return Equipment
   */
  public function setCode($code)
  {
    $this->code = $code;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getDesignation()
  {
    return $this->designation;
  }

  /**
   * @param mixed $designation
   * @return Equipment
   */
  public function setDesignation($designation)
  {
    $this->designation = $designation;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getCategorie()
  {
    return $this->categorie;
  }

  /**
   * @param mixed $categorie
   * @return Equipment
   */
  public function setCategorie($categorie)
  {
    $this->categorie = $categorie;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getPrix()
  {
    return $this->prix;
  }

  /**
   * @param mixed $prix
   * @return Equipment
   */
  public function setPrix($prix)
  {
    $this->prix = $prix;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getNotes()
  {
    return $this->notes;
  }

  /**
   * @param mixed $notes
   * @return Equipment
   */
  public function setNotes($notes)
  {
    $this->notes = $notes;
    return $this;
  }

}