<?php

namespace Drupal\os2loop_member_list\Helper;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\views\ViewExecutable;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * MailHelper for creating mail templates.
 */
class MemberListHelper implements ContainerInjectionInterface {
  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs an flag content form.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   The current user.
   */
  public function __construct(AccountProxyInterface $currentUser) {
    $this->currentUser = $currentUser;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
    );
  }

  /**
   * Implements hook_views_query_alter().
   */
  public function queryAlter(ViewExecutable $view, QueryPluginBase $query) {
    if ('contact_list' === $view->id()) {
      if ($this->currentUser->isAuthenticated()) {
        // @phpstan-ignore-next-line
        foreach ($query->where[1]['conditions'] as $index => $condition) {
          if ('user__os2loop_user_external_list.os2loop_user_external_list_value' === $condition['field']) {
            // @phpstan-ignore-next-line
            unset($query->where[1]['conditions'][$index]);
          }
        }
      }
    }
  }

  /**
   * Implements hook_views_form_alter().
   */
  public function formAlter(&$form, $form_id) {
    if ('user_form' === $form_id) {
      if (isset($form["os2loop_user_internal_list"], $form["os2loop_user_external_list"])) {
        // Enable field "Show in public contact list" only if "Show in contact
        // list" is checked.
        $form['os2loop_user_external_list']['#states'] = [
          'enabled' => [
            ':input[name="os2loop_user_internal_list[value]"]' => ['checked' => TRUE],
          ],
        ];
      }
    }
  }

}
