<?php

namespace Drupal\os2loop\Helper;

use Drupal\block\Entity\Block;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Drupal\os2loop\Form\SettingsForm;

/**
 * The helper.
 */
class Helper {
  /**
   * The config.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  private $config;

  /**
   * The constructor.
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->config = $configFactory->get(SettingsForm::SETTINGS);
  }

  /**
   * Implements hook_entity_create_access().
   *
   * Checks if a node type is enabled (cf self::contentTypeAccess()).
   */
  public function entityCreateAccess(AccountInterface $account, array $context, $entity_bundle): AccessResult {
    if ('node' === $context['entity_type_id']) {
      return $this->contentTypeAccess($entity_bundle);
    }

    return AccessResult::neutral();
  }

  /**
   * Implements hook_node_access().
   *
   * Checks if a node type is enabled (cf self::contentTypeAccess()).
   */
  public function nodeAccess(NodeInterface $node, $op, AccountInterface $account): AccessResult {
    return $this->contentTypeAccess($node->bundle());
  }

  /**
   * Implements hook_form_alter().
   *
   * Hides field for disabled taxonomies from a form.
   */
  public function formAlter(&$form, FormStateInterface $form_state, $form_id) {
    $this->hideTaxonomyVocabularies($form);
  }

  /**
   * Implements hook_preprocess_node().
   *
   * Hides disabled taxonomies from a node.
   */
  public function preprocessNode(array &$variables) {
    if (isset($variables['content'])) {
      $this->hideTaxonomyVocabularies($variables['content']);
    }
    if (isset($variables['elements'])) {
      $this->hideTaxonomyVocabularies($variables['elements']);
    }
  }

  /**
   * Implements hook_block_access().
   *
   * Hides disabled blocks.
   */
  public function blockAccess(Block $block, $operation, AccountInterface $account) {
    if ('view' === $operation) {
      $enabledTaxonomyVocabularies = array_filter($this->config->get('taxonomy_vocabulary') ?: [],
        static function ($value) {
          return 0 !== $value;
        });
      // Vocabulary name => Block id.
      $vocabularyBlocks = [
        'os2loop_subject' => 'os2loop_search_db_subject',
        'os2loop_tag' => 'os2loop_search_db_tags',
        'os2loop_profession' => 'os2loop_search_db_profession',
      ];
      foreach ($vocabularyBlocks as $vocabularyName => $blockId) {
        if ($blockId === $block->id() && !isset($enabledTaxonomyVocabularies[$vocabularyName])) {
          return AccessResult::forbidden();
        }
      }
    }

    return AccessResult::neutral();
  }

  /**
   * Check access to a node type.
   */
  private function contentTypeAccess(string $type) {
    $enabledNodeTypes = array_filter($this->config->get('node_type') ?: [], static function ($value) {
      return 0 !== $value;
    });

    if (!isset($enabledNodeTypes[$type])) {
      return AccessResult::forbidden(sprintf('Content type %s is not enabled', $type));
    }

    return AccessResult::neutral();
  }

  /**
   * Hide taxonomy vocabularies.
   *
   * @param array $element
   *   The element.
   */
  private function hideTaxonomyVocabularies(array &$element) {
    $enabledTaxonomyVocabularies = array_filter($this->config->get('taxonomy_vocabulary') ?: [], static function ($value) {
      return 0 !== $value;
    });

    // Vocabulary name => Field name.
    $vocabularyFields = [
      'os2loop_subject' => 'os2loop_shared_subject',
      'os2loop_tag' => 'os2loop_shared_tags',
      'os2loop_profession' => 'os2loop_shared_profession',
    ];

    foreach ($vocabularyFields as $vocabularyName => $fieldName) {
      if (isset($element[$fieldName]) && !isset($enabledTaxonomyVocabularies[$vocabularyName])) {
        unset($element[$fieldName]);
      }
    }
  }

}
