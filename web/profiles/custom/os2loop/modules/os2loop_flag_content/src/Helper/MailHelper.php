<?php

namespace Drupal\os2loop_flag_content\Helper;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * MailHelper for creating mail templates.
 */
class MailHelper {
  use StringTranslationTrait;

  /**
   * Implements hook_mail().
   */
  public function mail($key, &$message, $params) {
    $options = [
      'langcode' => $message['langcode'],
    ];
    switch ($key) {
      case 'flag_content':
        $subject = $this->t('Flagged content: @reason with @title', [
          '@title' => $params['node_title'],
          '@reason' => $params['reason'],
        ], $options);
        $message['subject'] = $subject;
        $message['body'][] = $params['message'];
        break;
    }
  }

}
