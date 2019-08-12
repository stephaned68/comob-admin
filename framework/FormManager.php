<?php


namespace m2i\framework;


class FormManager
{

  /**
   * @var string
   */
  private $title;

  /**
   * @var array
   */
  private $formFields;

  /**
   * @var string
   */
  private $indexRoute;

  /**
   * @var string
   */
  private $deleteRoute;

  /**
   * @return string
   */
  public function getTitle(): string
  {
    return $this->title;
  }

  /**
   * @param string $title
   * @return FormManager
   */
  public function setTitle(string $title): FormManager
  {
    $this->title = $title;
    return $this;
  }

  /**
   * @return array
   */
  public function getFormFields(): array
  {
    return $this->formFields;
  }

  /**
   * @return string
   */
  public function getIndexRoute(): string
  {
    return $this->indexRoute;
  }

  /**
   * @param string $indexRoute
   * @return FormManager
   */
  public function setIndexRoute(string $indexRoute): FormManager
  {
    $this->indexRoute = $indexRoute;
    return $this;
  }

  /**
   * @return string
   */
  public function getDeleteRoute(): string
  {
    return $this->deleteRoute;
  }

  /**
   * @param string $deleteRoute
   * @return FormManager
   */
  public function setDeleteRoute(string $deleteRoute): FormManager
  {
    $this->deleteRoute = $deleteRoute;
    return $this;
  }

  /**
   * FormManager constructor.
   * @param array $formFields
   */
  public function __construct(array $formFields = [])
  {
    $this->formFields = $formFields;
  }

  /**
   * Check if form has been submitted
   * @param string $submit
   * @return bool
   */
  public static function isSubmitted($submit = "submit")
  {
    return filter_has_var(INPUT_POST, $submit);
  }


  /**
   * Add a FormField object to the collection
   * @param $props
   * @return FormManager
   */
  public function addField($props)
  {
    $field = new FormField();
    $field
      ->setName($props["name"] ?? "")
      ->setLabel($props["label"] ?? $props["name"])
      ->setFilter($props["filter"] ?? FILTER_SANITIZE_STRING)
      ->setRequired($props["required"] ?? true)
      ->setErrorMessage($props["errorMessage"] ?? "{$props['label']} non saisi(e)")
      ->setControlType($props["controlType"] ?? "text")
      ->setCssClass($props["cssClass"] ?? "form-control")
      ->setPrimeKey($props["primeKey"] ?? false)
      ->setValueList($props["valueList"] ?? [])
    ;

    $this->formFields[$props["name"]] = $field;

    return $this;
  }

  /**
   * Return the FormField object for a given field name
   * @param $name
   * @return FormField
   */
  public function getField($name)
  {
    return $this->formFields[$name];
  }

  /**
   * Check if form is valid
   * @return bool
   */
  public function isValid()
  {
    return (count($this->validateForm()) == 0);
  }

  /**
   * Check form fields and return a list of errors, if any
   * @return array
   */
  public function validateForm()
  {
    $errorList = [];

    foreach ($this->formFields as $field) {
      $fieldValue = filter_input(INPUT_POST, $field->getName(), $field->getFilter());
      if (trim($fieldValue) === "" && $field->isRequired()) {
        array_push($errorList, $field->getErrorMessage());
      }
    }

    return $errorList;
  }

  /**
   * Convert POSTed data to an associative array
   * @return array
   */
  public function getData()
  {

    $formData = [];
    foreach ($this->formFields as $field) {
      $formData[$field->getName()] = filter_input(INPUT_POST, $field->getName(), $field->getFilter());
    }

    return $formData;
  }

  /**
   * @param array $data
   * @return string
   */
  public function render($data = [])
  {
    $formHTML = "";

    foreach ($this->formFields as $field) {
      $name = $field->getName();
      $value = null;
      if (array_key_exists($name, $data)) {
        $value = $data[$name];
      }
      $formHTML .= $field->render($value) . "\n";
    }

    return $formHTML;
  }

  public function renderButtons($data = [])
  {

    $options["btnSubmit"] = count($data) == 0 ? "Ajouter" : "Modifier";

    if (!empty($this->indexRoute)) {
      $options["indexRoute"] = $this->indexRoute;
    }

    if (!empty($this->deleteRoute) && count($data) > 0) {
      $options["deleteRoute"] = $this->deleteRoute;
    }

    ob_start();

    extract($options);

    require VIEWS_PATH . "/_fragments/form-buttons.phtml";

    return ob_get_clean();

  }

}