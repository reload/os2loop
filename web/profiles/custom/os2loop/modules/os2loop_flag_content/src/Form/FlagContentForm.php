<?php

namespace Drupal\os2loop_flag_content\Form;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\os2loop_flag_content\Services\ConfigService;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;

/**
 * Flag content form.
 *
 * @package Drupal\os2loop_flag_content\Form
 */
class FlagContentForm extends FormBase implements ContainerInjectionInterface {
  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The route mathcer.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatcher;

  /**
   * The mail manager.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * The mail manager.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Config service for flag content admin settings.
   *
   * @var \Drupal\os2loop_flag_content\Services\ConfigService
   */
  protected $configService;

  /**
   * Constructs an flag content form.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatcher
   *   The route matcher.
   * @param \Drupal\Core\Mail\MailManagerInterface $mailManager
   *   The mail manager.
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   The current user.
   * @param \Drupal\os2loop_flag_content\Services\ConfigService $configService
   *   The config for flag content.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, RouteMatchInterface $routeMatcher, MailManagerInterface $mailManager, AccountProxyInterface $currentUser, ConfigService $configService) {
    $this->entityTypeManager = $entityTypeManager;
    $this->routeMatcher = $routeMatcher;
    $this->mailManager = $mailManager;
    $this->currentUser = $currentUser;
    $this->configService = $configService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('current_route_match'),
      $container->get('plugin.manager.mail'),
      $container->get('current_user'),
      $container->get('os2loop_flag_content.config_service'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'flag_form_button';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $flag_config = $this->configService->getFlagContentSettings();
    $nid = $this->routeMatcher->getRawParameter('node');
    $form['node'] = [
      '#type' => 'hidden',
      '#value' => $nid,
    ];

    $config_reasons = array_values(array_map('trim', explode(PHP_EOL, $flag_config->get('reasons'))));
    $reasons = array_combine($config_reasons, $config_reasons);

    $form['reason'] = [
      '#type' => 'select',
      '#title' => $this->t('Reasons'),
      '#required' => TRUE,
      '#options' => $reasons,
      '#empty_option' => $this->t('Pick a reason'),
    ];

    $form['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Message'),
      '#required' => TRUE,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
      '#attributes' => [
        'class' => [
          'button',
        ],
      ],
    ];

    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#url' => new Url('entity.node.canonical', ['node' => $nid]),
      '#title' => $this->t('Cancel'),
      '#attributes' => [
        'class' => [
          'button',
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $nid = $form_state->getValue('node') ?? $this->routeMatch->getRawParameter('node');
    $node = $this->entityTypeManager->getStorage('node')->load($nid);
    $message = $form_state->getValue('message');
    $flag_config = $this->configService->getFlagContentSettings();
    $to = $flag_config->get('to_email');
    $module = 'os2loop_flag_content';
    $key = 'flag_content';
    $params['reason'] = $form_state->getValue('reason');
    $params['message'] = $message;
    $params['node_title'] = $node->label();
    $langcode = $this->currentUser->getPreferredLangcode();
    $send = TRUE;
    $this->mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
  }

}
