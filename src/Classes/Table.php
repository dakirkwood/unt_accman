<?php

namespace Drupal\unt_accman\Classes;

use Drupal\unt_accman\Classes\Container;

class Table extends Container {

  // Table specific properties
  public $caption = '';
  public $header_row = [];
  public $header_column = [];
  public $footer = [];

  // Table specific methods
  public function __construct(\DOMElement $tag)
  {
    parent::__construct($tag);
    $this->status = in_array('table', $this->etags, TRUE) ? 'enforce' : 'validate';

    if(!isset($this->children['thead']) || !isset($this->children['caption']) || !isset($this->children['tbody'])){
      $this->grade = $this->status == 'enforce' ? 'FAIL' : 'WARNING';
      if(!isset($this->children['caption'])){
        $this->issues[] = array(
          'text' => 'Tables must have a caption describing the data contained within.',
          'resource' => $this->resource_link($this->resources['table']['caption_attr']),
        );
//        $this->issues[] = 'Tables must have a caption describing the data contained within.';
      }
      if(!isset($this->children['thead'])){
        $this->issues[] = array(
          'text' => 'Tables must have a header row designating the data in the column.',
          'resource' => $this->resource_link($this->resources['table']['table_headers']),
        );
//        $this->issues[] = 'Tables must have a header row designating the data in the column.';
      }
      if(!isset($this->children['tbody'])){
        $this->issues[] = array(
          'text' => 'Tables must a body element containing data that corresponds to the header rows.',
          'resource' => $this->resource_link($this->resources['table']['structure']),
        );
//        $this->issues[] = 'Tables must a body element containing data that corresponds to the header rows.';
      }
    }
  }

}

?>
