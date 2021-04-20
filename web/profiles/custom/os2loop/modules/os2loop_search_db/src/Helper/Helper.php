<?php

namespace Drupal\os2loop_search_db\Helper;

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
   * @return array
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

}
