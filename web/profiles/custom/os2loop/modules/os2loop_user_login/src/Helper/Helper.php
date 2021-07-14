<?php

namespace Drupal\os2loop_user_login\Helper;

use Drupal\Core\Entity\EntityFieldManager;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\os2loop_user_login\Form\SettingsForm;
use Drupal\os2loop_settings\Settings;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

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
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entityTypeManager;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManager
   */
  private $entityFieldManager;

  /**
   * The Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  private $messenger;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  private $requestStack;

  /**
   * Constructor.
   */
  public function __construct(Settings $settings, EntityTypeManagerInterface $entity_type_manager, EntityFieldManager $entity_field_manager, MessengerInterface $messenger, RequestStack $requestStack) {
    $this->config = $settings->getConfig(SettingsForm::SETTINGS_NAME);
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->messenger = $messenger;
    $this->requestStack = $requestStack;
  }

  /**
   * Implements hook_form_alter().
   *
   * Show different login options depending on the site configuration.
   */
  public function alterForm(&$form, FormStateInterface $form_state, $form_id) {
    if ('openid_connect_login_form' === $form_id) {
      $form['#submit'][] = [$this, 'redirect'];
      if (!$this->config->get('show_oidc_login')) {
        $form['#access'] = FALSE;
      }
    }
    elseif ('user_login_form' === $form_id) {
      $form['#submit'][] = [$this, 'redirect'];
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
   * Redirect users after login.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The state of the form.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function redirect(array $form, FormStateInterface $form_state) {
    $url = Url::fromRoute('<front>');
    $parameters = $form_state->getRedirect()->getRouteParameters();

    // Check if there are empty required fields on the user that is
    // attempting to login.
    if (isset($parameters['user']) && is_numeric($parameters['user']) && $this->userHasEmptyRequiredFields($parameters['user'])) {
      $url = Url::fromRoute('entity.user.edit_form', $parameters);
      $this->messenger->addWarning($this->t('Please fill the required fields and save your profile.'));
    }

    // Check if a destination is already set.
    $request = $this->requestStack->getCurrentRequest();
    if (!$request->request->has('destination')) {
      $form_state->setRedirectUrl($url);
    }
    else {
      $request->query->set('destination', $request->request->get('destination'));
    }
  }

  /**
   * Check if a user has empty required fields.
   *
   * @param int $uid
   *   The id of the user to check.
   *
   * @return bool
   *   True if the user has empty required fields.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function userHasEmptyRequiredFields(int $uid): bool {
    /** @var \Drupal\user\Entity\User $user */
    $user = $this->entityTypeManager->getStorage('user')->load($uid);
    $fields = $this->entityFieldManager->getFieldDefinitions('user', 'user');

    foreach ($fields as $field_name => $field) {
      if ($field->isRequired()) {
        if (empty($user->get($field_name)->getValue())) {
          return TRUE;
        }
      }
    }

    return FALSE;
  }

}
