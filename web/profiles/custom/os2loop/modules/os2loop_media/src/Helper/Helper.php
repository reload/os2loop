<?php

namespace Drupal\os2loop_media\Helper;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\views\Plugin\views\query\Sql;
use Drupal\views\ViewExecutable;

/**
 * The helper.
 */
class Helper {
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
   * Implements hook_views_query_alter().
   *
   * Change query for media library.
   * The view config sets condition for viewing only media created by users with
   * more than the authenticated role.
   *
   * @todo Move the role condition into this alter query and add a specific permission for viewing media added by users with multple roles.
   *
   * @param \Drupal\views\ViewExecutable $view
   *   The view.
   * @param \Drupal\views\Plugin\views\query\QueryPluginBase $query
   *   The query.
   */
  public function queryAlter(ViewExecutable $view, QueryPluginBase $query) {
    if ($query instanceof Sql && ('media_library' === $view->id()) || ('media' === $view->id())) {
      $currentUserRoles = $this->currentUser->getRoles();

      // Allow display of all files for users with permission.
      if ($this->currentUser->hasPermission('view all files in media browser')) {
        $query = $this->removeRoleRestriction($query);
      }

      // Show only own files for users that are just authenticated
      // (but not user 1).
      if (1 === count($currentUserRoles) && in_array('authenticated', $currentUserRoles) && '1' !== $this->currentUser->id()) {
        $groupId = $query->setWhereGroup();
        // @phpstan-ignore-next-line
        $query->addWhere($groupId, 'media_field_data.uid', $this->currentUser->id(), '=');
        $query = $this->removeRoleRestriction($query);
      }
    }
  }

  /**
   * Remove role restriction from query.
   *
   * @param \Drupal\views\Plugin\views\query\QueryPluginBase $query
   *   The query.
   *
   * @property mixed $where
   *
   * @return \Drupal\views\Plugin\views\query\QueryPluginBase
   *   The modified query.
   */
  private function removeRoleRestriction(QueryPluginBase $query): QueryPluginBase {
    // Remove role restriction.
    // @phpstan-ignore-next-line
    foreach ($query->where[1]['conditions'] as $key => $condition) {
      if ('users_field_data_media_field_data__user__roles.roles_target_id' === $condition['field']) {
        // @phpstan-ignore-next-line
        unset($query->where[1]['conditions'][$key]);
      }
    }

    return $query;
  }

}
