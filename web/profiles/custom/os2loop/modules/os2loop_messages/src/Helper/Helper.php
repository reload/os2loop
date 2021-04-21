<?php

namespace Drupal\os2loop_messages\Helper;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\message\Entity\Message;
use Drupal\node\NodeInterface;
use Drupal\comment\CommentInterface;

/**
 * Os2Loop messages helper.
 *
 * Helper class for creating messages.
 */
class Helper extends ControllerBase {

  /**
   * Create a message.
   *
   * @param string $template_name
   *   The name of the message template.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity being to relate to.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function createMessage(string $template_name, EntityInterface $entity) {
    $message = Message::create(['template' => $template_name]);
    if ($entity instanceof NodeInterface) {
      $message->set('os2loop_message_node_refer', $entity);
    }
    elseif ($entity instanceof CommentInterface) {
      $node_storage = $this->entityTypeManager()->getStorage('node');
      $node = $node_storage->load($entity->get('entity_id')->getValue()[0]['target_id']);
      $message->set('os2loop_message_node_refer', $node);
    }
    $message->save();
  }

}
