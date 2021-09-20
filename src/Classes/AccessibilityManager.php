<?php

namespace Drupal\unt_accman\Classes;

use Drupal\unt_accman\Classes\Anchor;
use Drupal\unt_accman\Classes\Header;
use Drupal\unt_accman\Classes\Image;
use Drupal\unt_accman\Classes\Table;

use Drupal\Component\Utility\Html;

class AccessibilityManager {

  public $node = [];
  public $nid = '';
  public $body = '';
  public $mod_body = '';
  public $sections = [];
  public $mod_sections = [];
  public $tag_names = array('a','img','table');
  public $tags_processed = [];
  public $table = [];
  public $anchor = [];
  public $image = [];
  public $header = [];
  public $pass = [];
  public $fail = [];
  public $notice = [];
  public $doc_structure = [];
  public $headers = [];
  public $errors = [];
  public $warnings = [];
  public $operation = 'view';
  // Reporting variables
  public $validate = [];
  public $enforce = [];
  public $needsEnforcement = NULL;
//  public $verify_anchors = FALSE;
//  public $verify_images = FALSE;
//  public $verify_headers = FALSE;
//  public $verify_tables = FALSE;
  // Enforcement variables
//  public $enforce_anchors = FALSE;
//  public $enforce_images = FALSE;
//  public $enforce_headers = FALSE;
//  public $enforce_tables = FALSE;



  //public function __construct(Array $entity, $operation)
  public function __construct( $field_value, $operation)
  {
    //$config = \Drupal::service('config.factory')->getStorage('accmanconfig');
    $config = \Drupal::config('unt_accman.accmanconfig');
    // Store the node info
    //$this->node = $entity;
    // Store the nid for data-saving purposes
    //$this->nid = $entity['nid'][0]['value'];
    // Store the operation for which this object is being created
    $this->operation = $operation;
    // Get enabled reporting values
    $this->validate = $config->get('reporting');
    $this->enforce = $config->get('enforcement');
//    $this->verify_anchors = $config->get('accman_anchor_report');
//    $this->verify_images = $config->get('accman_image_report');
//    $this->verify_headers = $config->get('accman_header_report');
//    $this->verify_tables = $config->get('accman_table_report');
    // Get the enabled enforcement values
//    $this->enforce_anchors = $config->get('accman_anchor_enforce');
//    $this->enforce_images = $config->get('accman_image_enforce');
//    $this->enforce_headers = $config->get('accman_header_enforce');
//    $this->enforce_tables = $config->get('accman_table_enforce');
    // Store the original Body content
    //$this->body = !empty($entity['body']) ? trim($entity['body'][0]['value']) : '';
    $this->body = $field_value;//ksm($this->body);
    // Store the original Section panels content
//    if(!empty($entity->field_section_panel)){
//      foreach($entity->field_section_panel['und'] as $key=>$value){
//        if(isset($value['value']) && !empty($value['value'])){
//          $this->sections[$key] = $value['value'];
//        }
//      }
//    }
    // Process and store the Body value
    $this->mod_body = $this->processValue($this->body);
    // Process and store the value of each Section panel
//    foreach($this->sections as $key=>$section){
//      $this->mod_sections[ $key ] = $this->processValue($section);
//    }
    $failed_tags = array_keys($this->errors);
    foreach($failed_tags as $tag){
      if( in_array($tag, $this->enforce, TRUE) ){
        $this->needsEnforcement = TRUE;
      }
    }
  }

  /**
   * Creates a DOMDocument object from the $content string.
   *
   * @param string $content - The content of the Body field.
   * @return \DOMDocument
   *
   */
  private function makeDocument($content){
    if(!$content || empty($content)){ return FALSE; }
    //$doc = new DOMDocument(); // Create an empty DOMDocument object
    //$doc = HTML5::__construct;

    if ($content && !empty($content)) {
      // Load the content of the body field
      //@$doc->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
      //$doc->preserveWhiteSpace = FALSE;
      $doc = Html::load($content);//, ['HTML-ENTITIES', 'UTF-8']
    }

    return $doc;
  }

  /**
   * Process the content of the body; replace <a> tags with <span> tags.
   *
   * @param \DOMDocument $doc
   * @return string
   *
   */
  private function processValue($content){
    if(!$content || empty($content)){ return; }
    $doc = $this->makeDocument($content);
    if($doc){
      $doc_body = $doc->getElementsByTagName('body');
      if(in_array('header', $this->validate) || in_array('header', $this->enforce)){//$this->verify_headers){
        $this->scanHeaders($doc_body->item(0));
      }
      $this->verifyElements($doc);
    }

    if(!empty($this->fail)){
      $issue_que = [];
      foreach($this->fail as $element){
        foreach($element['issues'] as $issue){
          if(!isset($this->errors[ $element['class']]) || !in_array( $issue, $this->errors[ $element['class'] ] )){
            $this->errors[ $element['class'] ][] = $issue;
          }
        }
      }
    }
    if(!empty($this->notice)){
      $issue_que = [];
      foreach($this->notice as $element){
        foreach($element['issues'] as $issue){
          if(!isset($this->warnings[ $element['class']]) || !in_array( $issue, $this->warnings[ $element['class'] ] )){
            $this->warnings[ $element['class'] ][] = $issue;
          }
        }
      }
    }
    return $this->stripBodyTag($doc);
  }

  /**
   *
   * Scans the documents for elements named in the tag_names array,
   * verifies them for compliance and adds them to the issues array if appropriate.
   *
   * @param \DOMDocument $doc
   * @return \DOMDocument
   *
   */
  private function verifyElements(\DOMDocument $doc){
    foreach($this->tag_names as $tag_name){
      foreach($doc->getElementsByTagName($tag_name) as $tag){

        switch ($tag_name) {
          case 'a':
            $el = in_array('anchor', $this->validate, TRUE) || in_array('anchor', $this->enforce, TRUE) ? new Anchor($tag) : null;
            break;

          case 'img':
            $el = in_array('image', $this->validate, TRUE) || in_array('image', $this->enforce, TRUE) ? new Image($tag) : null;
            break;

          case 'table':
            $el = in_array('table', $this->validate, TRUE) || in_array('table', $this->enforce, TRUE) ? new Table($tag) : null;
            break;
        }
        if($el){
          // Add the $el (element) to the list of all tags processed
          $this->tags_processed[] = $el;
          // Store the index of the tags_processed array
          $index = count($this->tags_processed) - 1;
          // Get the $el class name
          $class = strtolower( get_class($el) );
          // Strip the namespace
          preg_match('/\\\(\w+)$/', $class, $match);
          // Set the class name to the last part of the namespace: anchor, table, etc
          $class = $match[1];
          // Add to the appropriate issues array with the same
          // key as in the tags_processed array
          if($el->grade == 'FAIL'){
            $this->$class['fail'][$index]['issues'] = $el->issues;

            switch ($class) {
              case 'anchor':
                $this->$class['fail'][$index]['inner_text'] = $el->innerText;
                break;

              case 'image':
                $this->$class['fail'][$index]['src'] = $el->attributes['src'];
                break;

            }
            $this->fail[$index]['class'] = $class;
            $this->fail[$index]['issues'] = $el->issues;
          }
          if($el->grade == 'WARNING'){
            $this->$class['warning'][$index]['issues'] = $el->issues;

            switch ($class) {
              case 'anchor':
                $this->$class['warning'][$index]['inner_text'] = $el->innerText;
                break;

              case 'image':
                $this->$class['warning'][$index]['src'] = $el->attributes['src'];
                break;

            }
            $this->notice[$index]['class'] = $class;
            $this->notice[$index]['issues'] = $el->issues;
          }
          // Set the attribute for easier identification
          if($this->operation != 'save'){
            $tag->setAttribute('data-untam-grade', $el->grade);
            $tag->setAttribute('data-untam-index', $index);
          }
          if($this->operation == 'save'){
            $tag->removeAttribute('data-untam-grade');
            $tag->removeAttribute('data-untam-index');
          }
        }
      }
    }
    return $doc;
  }

  /**
   * Strip the <body> tag that comes with the DOMDocument object.
   *
   * @param \DOMDocument $doc
   * @return string
   *
   */
  private function stripBodyTag(\DOMDocument $doc)
  {
    // We need to strip out the <body> tag which was added
    // when we created the DOMDocument object
    return preg_replace('/<[\/]?body>/', '', $doc->saveHTML($doc->getElementsByTagName('body')->item(0)));
  }

  /**
   *
   * Scans the document recursively for headers and adds
   * them to the doc_structure list in order of document appearance.
   *
   * @param \DOMElement $element
   *
   */
  private function scanHeaders(\DOMElement $element){

    foreach($element->childNodes as $el){
      // Empty this variable otherwise the value carries
      // over from the previous iteration
      $h = null;
      // DOMText nodes are of no interest
      if (get_class($el) == 'DOMText') { continue; }
      // If the element is a header
      if( preg_match('/h(\d)/', $el->nodeName) ){
        // Create the object
        $h = new Header($el);
        // Verify proper nesting against the previously logged header
        // The first header on the page needs special handling
        if (empty($this->headers)) {
          $h->verifyNesting();
        } else {
          $key = count($this->headers) - 1;
          $h->verifyNesting($this->headers[$key]);
        }
      }
      // For other container elements, we need to search recursively
      if($el->nodeName == 'div' || $el->nodeName == 'section' || $el->nodeName == 'article'){
        $this->scanHeaders($el);
      }
      // Log the header element to the list
      if($h){
        // Add to the list of headers used for verification on the next iteration
        $this->headers[] = $h;
        // Save info about the header to the doc_structure for high-level view of the order
        $this->doc_structure[] = $h->tagName . ': ' . $h->innerText;
        // Add to the master list of processed elements
        $this->tags_processed[] = $h;
        // Get the index of the master list
        $index = count($this->tags_processed) - 1;
        // Add to the issue que using the same key as in the master list
        if($h->grade == 'FAIL'){
          // Flag TRUE if this element is in the list of tags to enforce compliance
          //if(in_array('header', $this->enforce)){ $this->needsEnforcement = TRUE; }
          $this->header['fail'][] = array(
            'tag' => $h->tagName,
            'inner_text' => $h->innerText,
            'issues' => $h->issues
          );
          $this->fail[ $index ]['class'] = 'headers';
          $this->fail[ $index ]['issues'] = $h->issues;
        }
        if($h->grade == 'WARNING'){
          // Flag TRUE if this element is in the list of tags to enforce compliance
          //if(in_array('header', $this->enforce)){ $this->needsEnforcement = TRUE; }
          $this->header['warning'][] = array(
            'tag' => $h->tagName,
            'inner_text' => $h->innerText,
            'issues' => $h->issues
          );
          $this->notice[ $index ]['class'] = 'headers';
          $this->notice[ $index ]['issues'] = $h->issues;
        }
        // Set the attribute for identification
        if($this->operation != 'save'){
          $el->setAttribute('data-untam-grade', $h->grade);
          $el->setAttribute('data-untam-index', $index);
        }
      }
    }
  }

}

?>
