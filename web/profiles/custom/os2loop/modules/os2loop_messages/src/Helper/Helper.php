<?php

namespace Drupal\os2loop_messages\Helper;

use Drupal\comment\CommentInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\message\Entity\Message;
use Drupal\node\NodeInterface;

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
    if ($entity instanceof NodeInterface) {
      switch ($entity->bundle()) {
        case 'os2loop_documents_collection':
          $this->createMessage('os2loop_message_collection_ins', $entity);
          break;

        case 'os2loop_documents_document':
          $this->createMessage('os2loop_message_document_ins', $entity);
          break;

        case 'os2loop_question':
          $this->createMessage('os2loop_message_question_ins', $entity);
          break;

        case 'os2loop_post':
          $this->createMessage('os2loop_message_post_ins', $entity);
          break;
      }
    }
    elseif ($entity instanceof CommentInterface) {
      switch ($entity->bundle()) {
        case 'os2loop_question_answer':
          $this->createMessage('os2loop_message_answer_ins', $entity);
          break;

        case 'os2loop_post_comment':
          $this->createMessage('os2loop_message_comment_ins', $entity);
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
    if ($entity instanceof NodeInterface) {
      switch ($entity->bundle()) {
        case 'os2loop_documents_collection':
          $this->createMessage('os2loop_message_collection_upd', $entity);
          break;

        case 'os2loop_documents_document':
          $this->createMessage('os2loop_message_document_upd', $entity);
          break;

        case 'os2loop_question':
          $this->createMessage('os2loop_message_question_upd', $entity);
          break;

        case 'os2loop_post':
          $this->createMessage('os2loop_message_post_upd', $entity);
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
