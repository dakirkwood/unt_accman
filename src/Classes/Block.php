<?php

namespace Drupal\unt_accman\Classes;

use Drupal\unt_accman\Classes\Tag;

class Block extends Tag {

  // Block specific properties
  public $tagType = 'block';
  public $is_container = FALSE;

  // Block specific methods
  public function __construct(\DOMElement $tag)
  {
    parent::__construct($tag);
  }

}

?>
