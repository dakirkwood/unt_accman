<?php

namespace Drupal\unt_accman\Classes;

use Drupal\unt_accman\Classes\Block;

class Iframe extends Block {

  // iframe specific methods
  public function __construct(\DOMElement $tag)
  {
    parent::__construct($tag);

    if(isset($this->attributes['title']) && !empty($this->attributes['title'])){

    }
  }

}
