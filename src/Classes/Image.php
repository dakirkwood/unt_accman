<?php

namespace Drupal\unt_accman\Classes;

use Drupal\unt_accman\Classes\Tag;

class Image extends Tag {

  // Image specific properties

  // Image specific methods
  public function __construct(\DOMElement $tag)
  {
    parent::__construct($tag);

    if($this->parentNode->nodeName == 'a' && $this->parentNode->nodeValue == ''){
      if($this->hasAltText()){
        $this->grade = 'PASS';
      }
      else{
        $this->grade = 'FAIL';
        $this->issues[] = array(
          'text' => 'An image used as a link needs descriptive alt text about the information it links to.',
          'resource' => $this->resource_link($this->resources['image']['alt_attr']),
        );
//        $this->issues[] = 'An image used as a link needs descriptive alt text about the information it links to.';
//        $this->issue_resource = $this->resource_link($this->resources['image']['alt_attr']);
      }
    }else{
      if(isset($this->attributes['alt'])){
        $this->grade = $this->hasAltText() === 1 ? 'PASS' : 'WARNING';
        $this->issues[] = array(
          'text' => 'Image is marked as decorative.',
          'resource' => $this->resource_link($this->resources['image']['alt_attr']),
        );
//        $this->issues[] = 'Image is marked as decorative.';
//        $this->issue_resource = $this->resource_link($this->resources['image']['alt_attr']);
      }else{
        $this->grade = 'FAIL';
        $this->issues[] = array(
          'text' => 'The "alt" attribute is missing.',
          'resource' => $this->resource_link($this->resources['image']['alt_attr']),
        );
//        $this->issues[] = 'The "alt" attribute is missing.';
//        $this->issue_resource = $this->resource_link($this->resources['image']['alt_attr']);
      }
    }
  }

  public function hasAltText(){
    return isset($this->attributes['alt']) && !empty($this->attributes['alt'] && $this->attributes['alt'] != " ") ? 1 : 0;
  }

}

?>
