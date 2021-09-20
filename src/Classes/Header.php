<?php

namespace Drupal\unt_accman\Classes;

use Drupal\unt_accman\Classes\Block;

class Header extends Block {

  // Header specific properties
  public $innerText = '';
  public $level = null;

  // Header specific methods
  public function __construct(\DOMElement $tag)
  {
    parent::__construct($tag);
    $this->status = in_array('header', $this->etags, TRUE) ? 'enforce' : 'validate';

    $this->innerText = trim($this->removeNbSpaces($tag->nodeValue));
    preg_match('/h(\d)/', $this->tagName, $level);
    $this->level = $level[1];

    // Empty headers need to be deleted
    if(empty($this->innerText)){
      $this->issues[] = array(
        'text' => 'Delete empty headers.',
        'resource' => null,
      );
//      $this->issues[] = 'Delete empty headers.';
    }
    //$this->grade = 'PASS';

  }

  public function verifyNesting( $previous_header = null){

    // The first header in the document must be an H2
    if(!$previous_header){
      if($this->level > 2){
        $this->issues[] = array(
          'text' => 'The first header on the page must be an H2.',
          'resource' => null,
        );
//        $this->issues[] = 'The first header on the page must be an H2.';
      }
    }
    else{
      // The previous header level must be the same or the next level up
      if($this->level - $previous_header->level > 1 || $previous_header->grade == 'FAIL' || $previous_header->grade == 'WARNING'){
        $this->issues[] = array(
          'text' => 'Headers are not properly nested.',
          'resource' => $this->resource_link($this->resources['headers']['nesting']),
        );
//        $this->issues[] = 'Headers are not properly nested.';
      }
      // This header will fail if the previous header didn't pass
      /*if($previous_header->grade == 'FAIL' || $previous_header->grade == 'WARNING'){
        $this->issues[] = array(
          'text' => 'There is an issue with the header immediately above this one.',
          'resource' => null,
        );
      }*/
    }
    if(!empty($this->issues)){
      $this->grade = $this->status == 'enforce' ? 'FAIL' : 'WARNING';
    }
  }
}

?>
