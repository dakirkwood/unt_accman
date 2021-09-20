<?php

namespace Drupal\unt_accman\Classes;

use Drupal\unt_accman\Classes\Tag;

class Inline extends Tag {

  // Inline specific properties
  public $tagType = 'inline';
  public $innerText = '';

  // Inline specific methods
  public function __construct(\DOMElement $tag)
  {
    parent::__construct($tag);

    $this->innerText = $tag->nodeValue;

  }

}

?>
