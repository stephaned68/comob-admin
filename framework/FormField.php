<?php


namespace framework;


class FormField
{

  /**
   * @var string
   */
  private $name;

  /**
   * @var string
   */
  private $label;

  /**
   * @var int
   */
  private $filter;

  /**
   * @var bool
   */
  private $required;

  /**
   * @var mixed
   */
  private $defaultValue;

  /**
   * @var string
   */
  private $errorMessage;

  /**
   * @var string
   */
  private $controlType;

  /**
   * @var string
   */
  private $cssClass;

  /**
   * @var boolean
   */
  private $primeKey;

  /**
   * @var array
   */
  private $valueList;

  /**
   * @var array
   */
  private $size;

  /**
   * FormField constructor.
   * @param string $name
   * @param string $label
   * @param int $filter
   * @param bool $required
   * @param object $defaultValue
   * @param string $errorMessage
   * @param string $controlType
   * @param string $cssClass
   * @param bool $primeKey
   * @param array $valueList
   * @param array $size
   */
  public function __construct(
    string $name = null,
    string $label = null,
    int $filter = 0,
    bool $required = false,
    object $defaultValue = null,
    string $errorMessage = null,
    string $controlType = null,
    string $cssClass = null,
    bool $primeKey = false,
    array $valueList = [],
    array $size = []
  )
  {
    $this->name = $name;
    $this->label = $label;
    $this->filter = $filter;
    $this->required = $required;
    $this->defaultValue = $defaultValue;
    $this->errorMessage = $errorMessage;
    $this->controlType = $controlType;
    $this->cssClass = $cssClass;
    $this->primeKey = $primeKey;
    $this->valueList = $valueList;
    $this->size = $size;
  }


  /**
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * @param string $name
   * @return FormField
   */
  public function setName(string $name): FormField
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @return string
   */
  public function getLabel(): string
  {
    return $this->label;
  }

  /**
   * @param string $label
   * @return FormField
   */
  public function setLabel(string $label): FormField
  {
    $this->label = $label;
    return $this;
  }

  /**
   * @return int
   */
  public function getFilter(): int
  {
    return $this->filter;
  }

  /**
   * @param int $filter
   * @return FormField
   */
  public function setFilter(int $filter): FormField
  {
    $this->filter = $filter;
    return $this;
  }

  /**
   * @return bool
   */
  public function isRequired(): bool
  {
    return $this->required;
  }

  /**
   * @param bool $required
   * @return FormField
   */
  public function setRequired(bool $required): FormField
  {
    $this->required = $required;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getDefaultValue()
  {
    return $this->defaultValue;
  }

  /**
   * @param mixed $defaultValue
   * @return FormField
   */
  public function setDefaultValue($defaultValue)
  {
    $this->defaultValue = $defaultValue;
    return $this;
  }

  /**
   * @return string
   */
  public function getErrorMessage(): string
  {
    return $this->errorMessage;
  }

  /**
   * @param string $errorMessage
   * @return FormField
   */
  public function setErrorMessage(string $errorMessage): FormField
  {
    $this->errorMessage = $errorMessage;
    return $this;
  }

  /**
   * @return string
   */
  public function getControlType(): string
  {
    return $this->controlType;
  }

  /**
   * @param string $controlType
   * @return FormField
   */
  public function setControlType(string $controlType): FormField
  {
    $this->controlType = strtolower($controlType);
    return $this;
  }

  /**
   * @return string
   */
  public function getCssClass(): string
  {
    return $this->cssClass;
  }

  /**
   * @param string $cssClass
   * @return FormField
   */
  public function setCssClass(string $cssClass): FormField
  {
    $this->cssClass = $cssClass;
    return $this;
  }

  /**
   * @return bool
   */
  public function isPrimeKey(): bool
  {
    return $this->primeKey;
  }

  /**
   * @param bool $primeKey
   * @return FormField
   */
  public function setPrimeKey(bool $primeKey): FormField
  {
    $this->primeKey = $primeKey;
    return $this;
  }

  /**
   * @return array
   */
  public function getValueList(): array
  {
    return $this->valueList;
  }

  /**
   * @param array $valueList
   * @return FormField
   */
  public function setValueList(array $valueList): FormField
  {
    $this->valueList = $valueList;
    return $this;
  }

  /**
   * @return array
   */
  public function getSize(): array
  {
    return $this->size;
  }

  /**
   * @param array $size
   * @return FormField
   */
  public function setSize(array $size): FormField
  {
    $this->size = $size;
    return $this;
  }

  /**
   * @param $data
   * @return false|string
   */
  public function render($data): string
  {

    if ($this->controlType === "hidden") {
      return '<input type="hidden" name="' . $this->name . '" value="' . $data . '">';
    }

    if ($this->controlType === "number") {
      if ($this->filter !== FILTER_SANITIZE_NUMBER_INT && $this->filter !== FILTER_SANITIZE_NUMBER_FLOAT) {
        $this->filter = FILTER_SANITIZE_NUMBER_FLOAT;
      }
    }

    if ($this->controlType === "decimal" || isset($this->size["step"])) {
      $this->controlType = "number";
      $this->filter = FILTER_SANITIZE_NUMBER_FLOAT;
      if (!isset($this->size["step"])) {
        $this->size["step"] = "0.01";
      }
    }

    if ($this->filter === FILTER_SANITIZE_NUMBER_INT || $this->filter === FILTER_SANITIZE_NUMBER_FLOAT) {
      if ($this->controlType !== "hidden") {
        $this->controlType = "number";
      }
    }

    $options = [
      "fieldName" => $this->name,
      "fieldLabel" => $this->label ?? $this->name,
      "fieldType" => $this->controlType ?? "text",
      "fieldClass" => $this->cssClass ?? self::getDefaultCSS($this->controlType),
      "fieldSelect" => $this->valueList ?? [],
      "fieldSize" => $this->size,
      "fieldValue" => $data ?? $this->defaultValue
    ];

    if ($this->primeKey && $data) {
      $options["fieldClass"] = "form-control-plaintext";
      $options["fieldReadonly"] = "readonly";
    }

    ob_start();

    extract($options);

    if ($this->controlType === "select"
      || $this->controlType === "textarea"
      || $this->controlType === "checkbox") {
      require VIEWS_PATH . "/_fragments/form-{$this->controlType}.phtml";
    } else {
      require VIEWS_PATH . "/_fragments/form-group.phtml";
    }

    $fieldHTML = ob_get_clean();

    return $fieldHTML;
  }

  public static function getDefaultCSS($controlType)
  {
    $defaultCSS = [
      "hidden" => "",
      "text" => "form-control",
      "number" => "form-control",
      "decimal" => "form-control",
      "select" => "form-control",
      "textarea" => "form-control",
      "checkbox" => "form-check-input"
    ];

    return $defaultCSS[$controlType];
  }
}