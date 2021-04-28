<?php

namespace Drupal\os2loop_subscriptions\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller class for user subscriptions page.
 */
class UserSubscriptions extends ControllerBase {

  /**
   * Display content for user subscriptions.
   */
  public function content() {
    return [
      '#type' => 'markup',
      '#theme' => 'os2loop_subscriptions_user',
    ];
  }

}
