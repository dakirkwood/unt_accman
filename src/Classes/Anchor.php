<?php

namespace Drupal\unt_accman\Classes;

use Drupal\unt_accman\Classes\Inline;
use Drupal\unt_accman\Classes\Image;

class Anchor extends Inline {

  // Anchor specific properties
  public $anchorType = '';

  // Anchor specific methods
  public function __construct(\DOMElement $tag)
  {
    parent::__construct($tag);
    // Verify whether this is tag is being enforced or just validated
    $this->status = in_array('anchor', $this->etags, TRUE) ? 'enforce' : 'validate';

    // Do not test email links
    if(preg_match('/mailto/', $this->attributes['href'])){
      $this->anchorType = 'EMAIL';
    }
    elseif(strpos($this->attributes['href'], '#') === 0){
      $this->anchorType = 'PAGE_ANCHOR';
    }
    else{
      $this->anchorType = 'HYPERLINK';
      if($this->innerText != ''){
        $this->checkContext();
      }else{

        foreach($this->childNodes as $child){

          if($child->nodeName == 'img'){
            $img = new Image($child);
            if(!isset($img->attributes['alt']) || empty($img->attributes['alt'])){
              $this->grade = $this->status === 'enforce' ? 'FAIL' : 'WARNING';
//              $this->grade = 'FAIL';
              $this->issues[] = array(
                'text' => 'An image used as a link needs descriptive alt-attribute text about the destination it links to.',
                'resource' => $this->resource_link($this->resources['anchor']['image_links']),
              );
//              $this->issues[] = 'An image used as a link needs descriptive text about the information it links to.';
            }else{
              $this->grade = 'PASS';
            }
          }
        }
      }
    }
  }

  private function checkContext(){
    //$pattern = "/^([^(click|here|click here)][\.'\-\,\w]*\s[\-\,\w]*\s[\-\,\w]*\s?)/";
    $word = "[\&\;'’\:\.\-\,\w]*";
    $pattern = "/([^(click|here|click here)]" . $word . "\s" . $word . "\s" . $word . "\s?)/";

    $text = $this->removeNbSpaces($this->innerText);
    $this->grade = preg_match($pattern, $text, $matches) == 1 ? 'PASS' : ($this->status == 'enforce' ? 'FAIL' : 'WARNING');

    if($this->grade == 'FAIL' || $this->grade == 'WARNING'){
      $this->issues[] = array(
        'text' => 'Linked text needs to be more descriptive.',
        'resource' => $this->resource_link($this->resources['anchor']['link_text']),
      );
//      $this->issues[] = 'Linked text needs to be more descriptive.';
    }
  }

}

?>
