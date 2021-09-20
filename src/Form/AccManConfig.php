<?php

namespace Drupal\unt_accman\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AccManConfig.
 */
class AccManConfig extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'unt_accman.accmanconfig',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'acc_man_config';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('unt_accman.accmanconfig');
    $form['reporting'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Validation'),
      '#description' => $this->t('Select which tags you want to provide accessibility reporting (but not enforcement) for. <br />Reporting will allow editors to publish without resolving accessibility issues.'),
      '#options' => ['anchor' => $this->t('anchor'), 'header' => $this->t('header'), 'iframe' => $this->t('iframe'), 'image' => $this->t('image'), 'table' => $this->t('table')],
      '#default_value' => $config->get('reporting'),
    ];
    $form['enforcement'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Enforcement'),
      '#description' => $this->t('Select which tags you want to enforce accessibility compliance for.<br />Enforcement prevents publishing content until accessibility issues are resolved for selected tags.'),
      '#options' => ['anchor' => $this->t('anchor'), 'header' => $this->t('header'), 'iframe' => $this->t('iframe'), 'image' => $this->t('image'), 'table' => $this->t('table')],
      '#default_value' => $config->get('enforcement'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('unt_accman.accmanconfig')
      ->set('reporting', $form_state->getValue('reporting'))
      ->set('enforcement', $form_state->getValue('enforcement'))
      ->save();
  }

}
