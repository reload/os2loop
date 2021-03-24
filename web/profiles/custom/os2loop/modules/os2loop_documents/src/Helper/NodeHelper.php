<?php

namespace Drupal\os2loop_documents\Helper;

use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\RequestStack;

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
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  private $requestStack;

  /**
   * Constructor.
   */
  public function __construct(CollectionHelper $collectionHelper, RequestStack $requestStack) {
    $this->collectionHelper = $collectionHelper;
    $this->requestStack = $requestStack;
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
      $items = $this->collectionHelper->buildDocumentTree($items);
      $variables['os2loop_documents_collection_tree'] = $items;
    }
    elseif (self::CONTENT_TYPE_DOCUMENT === $node->getType()) {
      $collections = $this->collectionHelper->loadCollections($node);
      // Check if we have a request for a specific collection.
      $request = $this->requestStack->getCurrentRequest();
      $collectionId = NULL !== $request ? $request->query->get('collection') : NULL;
      if (isset($collections[$collectionId])) {
        $collections = [$collections[$collectionId]];
      }
      if (1 === count($collections)) {
        $collection = reset($collections);
        $items = $this->collectionHelper->loadTree($collection->id());
        $items = $this->collectionHelper->buildDocumentTree($items);
        $variables['os2loop_documents_collection'] = $collection;
        $variables['os2loop_documents_collection_tree'] = $items;
      }
      elseif (count($collections) > 1) {
        $variables['os2loop_documents_collections'] = $collections;
      }

      // Make sure that the "collection" query parameter is considered when
      // caching (cf.
      // https://www.drupal.org/docs/drupal-apis/cache-api/cache-contexts).
      $variables['#cache']['contexts'][] = 'url.query_args:collection';
    }
  }

}
