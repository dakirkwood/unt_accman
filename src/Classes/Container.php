<?php

namespace Drupal\unt_accman\Classes;

use Drupal\unt_accman\Classes\Block;

class Container extends Block {

  // Container specific properties
  public $is_container = TRUE;
  public $children = [];

  // Container specific methods
  public function __construct(\DOMElement $tag)
  {
    parent::__construct($tag);

    $this->children = $this->listChildren($tag);
    /*foreach($tag->childNodes as $child){
      $this->children[ $child->nodeName ] = $child->nodeValue;
    }*/
  }

  static function listChildren(\DOMElement $tag){
    $children = [];
    foreach($tag->childNodes as $child){
      $children[ $child->nodeName ] = $child->nodeValue;
    }
    return $children;
  }

}

?>
