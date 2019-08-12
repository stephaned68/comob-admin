<?php


namespace m2i\framework;


class View
{
  /**
   * @var string
   */
  private $layout;

  /**
   * @var array
   */
  private $data = [];

  /**
   * View constructor.
   * @param string $layout
   */
  public function __construct(string $layout = "")
  {
    $this->layout = $layout;
  }

  /**
   * @return string
   */
  public function getLayout(): string
  {
    return $this->layout;
  }

  /**
   * @param string $layout
   * @return View
   */
  public function setLayout(string $layout): View
  {
    $this->layout = $layout;
    return $this;
  }

  /**
   * @return array
   */
  public function getData(): array
  {
    return $this->data;
  }

  /**
   * @param array $data
   * @return View
   */
  public function setData(array $data): View
  {
    $this->data = $data;
    return $this;
  }

  private function getTemplateContent($template, $data = [])
  {
    ob_start();

    extract($data);

    require_once VIEWS_PATH . "/{$template}.phtml";

    return ob_get_clean();
  }

  public function render($template, $data = [])
  {
    $pageContent = $this->getTemplateContent($template, $data);
    $data["content"] = $pageContent;

    // insert specific javascript file
    // path schema is 'public/js/{controller}/{action}.js'
    $script = "js/$template.js";
    if (!file_exists(PUBLIC_PATH . "/" . $script)) {
      $script = "";
    }
    $data["script"] = $script;


    if (empty($this->layout)) {
      return $pageContent;
    } else {
      return $this->getTemplateContent($this->layout, $data);
    }

  }


}