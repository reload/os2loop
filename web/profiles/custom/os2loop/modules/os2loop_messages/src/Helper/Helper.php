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
   * Create message on insert entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity that is being created.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function entityInsert(EntityInterface $entity) {
    if ($entity->getEntityTypeId() == 'node') {
      switch ($entity->bundle()) {
        case 'os2loop_documents_collection':
          $this->createMessage('os2loop_message_collection_added', $entity);
          break;

        case 'os2loop_documents_document':
          $this->createMessage('os2loop_message_document_added', $entity);
          break;

        case 'os2loop_question':
          $this->createMessage('os2loop_message_question_added', $entity);
          break;

        case 'os2loop_post':
          break;
      }
    }

    if ($entity->getEntityTypeId() == 'comment') {
      switch ($entity->bundle()) {
        case 'os2loop_question_answer':
          $this->createMessage('os2loop_message_answer_added', $entity);
          break;

        case 'os2loop_post_comment':
          break;
      }
    }
  }

  /**
   * Create message on update entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity that is being updated.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function entityUpdate(EntityInterface $entity) {
    if ($entity->getEntityTypeId() == 'node') {
      switch ($entity->bundle()) {
        case 'os2loop_documents_collection':
          $this->createMessage('os2loop_message_collection_edit', $entity);
          break;

        case 'os2loop_documents_document':
          $this->createMessage('os2loop_message_document_edited', $entity);
          break;

        case 'os2loop_question':
          $this->createMessage('os2loop_message_question_edited', $entity);
          break;

        case 'os2loop_post':
          break;
      }
    }
  }

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
      $message->set('os2loop_message_comment_refer', $entity);
    }
    $message->save();
  }

}
