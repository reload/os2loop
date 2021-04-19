<?php

namespace Drupal\os2loop_flag_content\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure flag content admin settings for this site.
 */
class FlagContentAdminForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'os2loop_flag_content.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'os2loop_flag_content_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['reasons'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Reasons'),
      '#description' => $this->t('Write the possible reasons, separated by a newline'),
      '#default_value' => $config->get('reasons'),
    ];

    $form['to_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email address of recipient'),
      '#default_value' => $config->get('to_email'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $form_state->getValue('content_types');
    $this->configFactory->getEditable(static::SETTINGS)
      ->set('reasons', $form_state->getValue('reasons'))
      ->set('content_types', $form_state->getValue('content_types'))
      ->set('to_email', $form_state->getValue('to_email'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
