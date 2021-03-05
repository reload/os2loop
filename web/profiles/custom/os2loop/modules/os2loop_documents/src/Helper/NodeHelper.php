<?php

namespace Drupal\os2loop_documents\Helper;

use Drupal\node\NodeInterface;

/**
 * Node helper.
 */
class NodeHelper {
  public const CONTENT_TYPE_DOCUMENT = 'os2loop_documents_document';
  public const CONTENT_TYPE_COLLECTION = 'os2loop_documents_collection';

  /**
   * The collection helper.
   *
   * @var CollectionHelper
   */
  private $collectionHelper;

  /**
   * Constructor.
   */
  public function __construct(CollectionHelper $collectionHelper) {
    $this->collectionHelper = $collectionHelper;
  }

  /**
   * Implements hook_ENTITY_TYPE_update().
   */
  public function updateNode(NodeInterface $node) {
    // @todo Clear collection cache when document is updated.
  }

  /**
   * Implements hook_preprocess_HOOK().
   */
  public function preprocessNode(array &$variables) {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $variables['node'];
    if (self::CONTENT_TYPE_COLLECTION === $node->getType()) {
      $items = $this->collectionHelper->loadTree($node->id());
      $variables['content']['os2loop_documents_collection_tree'] = [
        '#type' => 'fieldset',
        '#title' => __METHOD__,
      ];

      foreach ($items as $id => $item) {
        $variables['content']['os2loop_documents_collection_tree'][$id] = [
          '#markup' => implode('; ', [
            $item->parent_id->value,
            $item->depth,
            $item->document_id->value,
          ]),
          '#prefix' => '<pre>',
          '#suffix' => '</pre>',
        ];
      }
    }
  }

}
