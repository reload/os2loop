<?php

namespace Drupal\os2loop_search_db\Helper;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\search_api\Query\Condition;
use Drupal\search_api\Query\ConditionGroupInterface;
use Drupal\search_api\Query\QueryInterface;
use Drupal\search_api_autocomplete\Suggestion\Suggestion;

/**
 * Search Api Autocomplete Helper.
 *
 * Hook implementations for search_api_autocomplete.
 */
class Helper {
  use StringTranslationTrait;

  /**
   * The config.
   *
   * @var array
   */
  private $config;

  /**
   * Constructor.
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->config = $configFactory->get('os2loop.settings')->get('os2loop_search_db') ?? [];
  }

  /**
   * Implements hook_search_api_autocomplete_suggestions_alter().
   *
   * Adds a suggestion, "See all results for …", linking to the full search
   * result for the current query.
   */
  public function alterSuggestions(array &$suggestions, array $alter_params) {
    /** @var string $user_input */
    $user_input = $alter_params['user_input'];

    $suggestion = (new Suggestion())
      ->setSuggestedKeys($user_input)
      ->setLabel($this->t('See all results for %user_input', ['%user_input' => $user_input]));

    $suggestions[] = $suggestion;
  }

  /**
   * Implements hook_search_api_query_alter().
   *
   * Alters query to include comments when filtering select content types.
   */
  public function alterSearchApiQuery(QueryInterface $query) {
    if ('os2loop_search_db_index' === $query->getIndex()->id()) {
      $typeGroups = $this->getConditionGroupsWithTag($query->getConditionGroup()->getConditions(), 'facet:type');
      $contentTypeGroups = $this->getContentTypeGroups();
      foreach ($typeGroups as $group) {
        // Get the content types in the facet filter.
        $types = array_filter(
            array_map(static function (Condition $condition) {
              return 'type' === $condition->getField() ? $condition->getValue() : NULL;
            }, $group->getConditions())
          );
        foreach ($types as $type) {
          switch ($type) {
            case 'os2loop_post':
              $group->addCondition('comment_type', 'os2loop_post_comment', '=');
              break;

            case 'os2loop_question':
              $group->addCondition('comment_type', 'os2loop_question_answer', '=');
              break;
          }

          if (isset($contentTypeGroups[$type])) {
            // Include other content types.
            $group->addCondition('type', $contentTypeGroups[$type], 'IN');
          }
        }
      }
    }
  }

  /**
   * Get condition groups with a specific tag in a query.
   *
   * @param \Drupal\search_api\Query\ConditionGroupInterface[] $conditions
   *   The conditions.
   * @param string $tag
   *   The tag to find.
   *
   * @return \Drupal\search_api\Query\ConditionGroupInterface[]
   *   Conditions with the tag.
   */
  private function getConditionGroupsWithTag(array $conditions, string $tag) {
    $groups = [];
    foreach ($conditions as $condition) {
      if ($condition instanceof ConditionGroupInterface && $condition->hasTag($tag)) {
        $groups[] = $condition;
      }
    }

    return $groups;
  }

  /**
   * Get groups of content types.
   *
   * Content types can be grouped under a single content type and handled os one
   * in facet filters.
   *
   * @return array
   *   The groups.
   */
  public function getContentTypeGroups(): array {
    return $this->config['content_type_groups'] ??
      [
        // "Document" includes "Collection" and "External".
        'os2loop_documents_document' => [
          'os2loop_documents_collection',
          'os2loop_external',
        ],
      ];
  }

}
