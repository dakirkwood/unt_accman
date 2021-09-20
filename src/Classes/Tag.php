<?php

namespace Drupal\unt_accman\Classes;

use Drupal\views\Plugin\views\argument\NullArgument;

class Tag {

  public $tagName = '';
  public $attributes = [];
  public $innerText = null;
  public $tagType = null;
  public $grade = null;
  public $issues = [];
  public $childNodes = null;
  public $parentNode = null;
  public $status = null; // Holds the values 'validate' or 'enforce'
  public $rtags = []; // Array of tags in the REPORT config
  public $etags = []; // Arracy of tags in the ENFORCE config
  public $resources = array(
    'anchor' => array(
      'target_attr' => '',
      'link_text' => 'https://webaim.org/techniques/hypertext/link_text',
      'image_links' => 'https://www.w3.org/WAI/tutorials/images/functional/',
    ),
    'headers' => array(
      'nesting' => 'https://www.w3.org/WAI/tutorials/page-structure/headings/', //'https://webaim.org/techniques/semanticstructure/#contentstructure',
    ),
    'image' => array(
      'alt_attr' => 'https://www.w3.org/WAI/tutorials/images/', //'https://webaim.org/techniques/alttext/',
      'link_alt_attr' => '',
    ),
    'iframe' => array(
      'title_attr' => 'https://webaim.org/techniques/frames/#iframe',
    ),
    'table' => array(
      'caption_attr' => 'https://www.w3.org/WAI/tutorials/tables/caption-summary/', //'https://webaim.org/techniques/tables/data#caption',
      'table_headers' => 'https://www.w3.org/WAI/tutorials/tables/', //'https://webaim.org/techniques/tables/data#th',
      'structure' => 'https://www.w3.org/WAI/tutorials/tables/',
    ),
  );

  public function __construct(\DOMElement $tag)
  {
    $config = \Drupal::config('unt_accman.accmanconfig');
    $this->rtags = $config->get('reporting');
    $this->etags = $config->get('enforcement');
    $this->tagName = $tag->nodeName;
    foreach ($tag->attributes as $attr){
      $this->attributes[ $attr->name ] = $attr->value;
    }
    $this->childNodes = $tag->childNodes;
    $this->parentNode = $tag->parentNode;
    if(empty($this->issues)){ $this->grade = 'PASS'; }

  }

  static function removeNbSpaces(string $text){
    $text = htmlentities($text, null, 'utf-8');

    return str_replace('&nbsp;', ' ', $text);
  }

  public function resource_link(string $resource){
    return "<a class=\"resource\" href=\"{$resource}\">How to resolve.</a>";
  }
}

?>
