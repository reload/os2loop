<?php

namespace Drupal\os2loop_user_login\Helper;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\os2loop_user_login\Form\SettingsForm;
use Drupal\os2loop_settings\Settings;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Helper for os2loop_user_login.
 */
class Helper {
  use StringTranslationTrait;

  /**
   * The config.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  private $config;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Constructor.
   */
  public function __construct(Settings $settings, ModuleHandlerInterface $module_handler) {
    $this->config = $settings->getConfig(SettingsForm::SETTINGS_NAME);
    $this->moduleHandler = $module_handler;
  }

  /**
   * Implements hook_form_alter().
   *
   * Show different login options depending on the site configuration.
   */
  public function alterForm(&$form, FormStateInterface $form_state, $form_id) {
    if ('openid_connect_login_form' === $form_id) {
      if (!$this->config->get('show_oidc_login')) {
        $form['#access'] = FALSE;
      }
    }
    elseif ('user_login_form' === $form_id) {
      if (!$this->config->get('show_drupal_login')) {
        $form['#attached']['library'][] = 'os2loop_user_login/user-login-form';

        // Wrap default Drupal login form in an element with a known id
        // (drupal-login) so we can visually hide it.
        foreach ($form as $key => $value) {
          if (0 !== strpos($key, '#')) {
            $form['drupal_login'][$key] = array_merge($value);
            unset($form[$key]);
          }
        }
        $form['drupal_login'] += [
          '#type' => 'fieldset',
          '#title' => $this->t('Drupal login'),
          '#weight' => 100,
          '#attributes' => ['id' => 'drupal-login'],
        ];
      }

      if ($this->config->get('show_saml_login')) {
        $form['saml_login'] = [
          '#weight' => -100,
          '#type' => 'link',
          '#title' => $this->t('Log in with SAML'),
          '#url' => Url::fromRoute('samlauth.saml_controller_login'),
          '#attributes' => [
            'class' => ['os2loop-user-login-button'],
          ],
        ];
      }
    }
  }

  /**
   * Remove "Connected accounts" tab on user profile and edit form.
   *
   * @param array $data
   *   The local tasks data.
   * @param string $route_name
   *   The current route.
   */
  public function alterLocalTasks(array &$data, string $route_name) {
    if ($this->moduleHandler->moduleExists('openid_connect')) {
      if ('entity.user.canonical' === $route_name || 'entity.user.edit_form' === $route_name) {
        foreach ($data['tabs'][0] as $key => $tab) {
          if ('entity.user.openid_connect_accounts' === $key) {
            unset($data['tabs'][0][$key]);
          }
        }
      }
    }
  }

}
