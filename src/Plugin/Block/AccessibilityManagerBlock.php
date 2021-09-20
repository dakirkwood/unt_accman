<?php

namespace Drupal\unt_accman\Plugin\Block;

use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'AccessibilityManagerBlock' block.
 *
 * @Block(
 *  id = "accessibility_manager_block",
 *  admin_label = @Translation("AccessibilityManager block"),
 * )
 */
class AccessibilityManagerBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {
    // Reference the global variable for values set by other methods
    global $accessibilityManager;
    //$report_status = NULL;
    /*if(){
      $message = 'AccessibilityManager is enforcing compliance on tags but reporting is turned off. Turn on reporting for all enforced tags.';
      \Drupal::messenger()->addWarning(t($message));
    }*/

    if($accessibilityManager){
      $error_count = count($accessibilityManager->fail);
      $warning_count = count($accessibilityManager->notice);
      $build['#show_report'] = $error_count > 0 || $warning_count > 0 ? 1 : 0;

      // Build the array of general issues for each tag category
      foreach($accessibilityManager->errors as $key=>$issue){
        $build['#errors'][$key] = $issue;
      }
      // Build the array of general issues for each tag category
      foreach($accessibilityManager->warnings as $key=>$issue){
        $build['#warnings'][$key] = $issue;
      }

      // Build the array of header failures
      if(isset($accessibilityManager->header['fail'])){
        foreach($accessibilityManager->header['fail'] as $key=>$feedback){
          $build['#header_error'][$key] = $feedback;
        }
      }
      // Build the array of header warnings
      if(isset($accessibilityManager->header['warning'])){
        foreach($accessibilityManager->header['warning'] as $key=>$feedback){
          $build['#header_warning'][$key] = $feedback;
        }
      }


      // Build the array of anchor failures
      if(isset($accessibilityManager->anchor['fail'])){
        foreach($accessibilityManager->anchor['fail'] as $key=>$feedback){
          $build['#anchor_error'][$key] = $feedback;
        }
      }
      // Build the array of anchor warnings
      if(isset($accessibilityManager->anchor['warning'])){
        foreach($accessibilityManager->anchor['warning'] as $key=>$feedback){
          $build['#anchor_warning'][$key] = $feedback;
        }
      }


      // Build the array of image failures
      if(isset($accessibilityManager->image['fail'])){
        foreach($accessibilityManager->image['fail'] as $key=>$feedback){
          $build['#image_error'][$key] = $feedback;
        }
      }
      // Build the array of image warnings
      if(isset($accessibilityManager->image['warning'])){
        foreach($accessibilityManager->image['warning'] as $key=>$feedback){
          $build['#image_warning'][$key] = $feedback;
        }
      }


      // Build the array of table errors
      if(isset($accessibilityManager->table['fail'])){
        foreach($accessibilityManager->table['fail'] as $key=>$feedback){
          $build['#table_error'][$key] = $feedback;
        }
      }
      // Build the array of table warnings
      if(isset($accessibilityManager->table['warning'])){
        foreach($accessibilityManager->table['warning'] as $key=>$feedback){
          $build['#table_warning'][$key] = $feedback;
        }
      }


      //$build = [];
      $build['#theme'] = 'accessibility_manager_block';
      $build['#error_count'] = $error_count;
      $build['#warning_count'] = $warning_count;

      return $build;
    }
  }

  /**
   * Helper function provides feedback to inform the admin/editor
   * what AccessibilityManager is checking for
   *
   * @param \Drupal\unt_accman\Plugin\Block\string $tag
   */
  public function tag_status(string $tag){
    global $accessibilityManager;
    $message = '';
    $report_status = NULL;
    $reporting = in_array($tag, $accessibilityManager->report, TRUE) ? 1 : 0;
    $enforcing = in_array($tag, $accessibilityManager->enforce, TRUE) ? 1 : 0;
    $status = $reporting <=> $enforcing;
    switch ($status) {
      // Tags are being enforced without the report block showing.
      // Returning FALSE will flag a message to the user that reporting should be turned on
      case -1:
        return FALSE;

      // Tags are being enforced WITH the report block showing.
      case 0:
        if($reporting == 1){
          return 'Accessibility issues in this category (' . $tag . ') will be enforced when editing.';
        }
        //break;

      // Tags are being reported but not enforced
      case 1:
        return 'Accessibility issues in this category (' . $tag . ') are not being enforced.';
        break;
    }
    //return $message;
  }

}
