<?php

namespace Drupal\os2loop_search_db\Helper;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\search_api_autocomplete\Suggestion\Suggestion;

/**
 * Search Api Autocomplete Helper.
 *
 * Hook implementations for search_api_autocomplete.
 */
class SearchApiAutocompleteHelper {
  use StringTranslationTrait;

  /**
   * Implements hook_search_api_autocomplete_suggestions_alter().
   */
  public function alterSuggestions(array &$suggestions, array $alter_params) {
    /** @var string $user_input */
    $user_input = $alter_params['user_input'];

    $suggestion = (new Suggestion())
      ->setSuggestedKeys($user_input)
      ->setLabel($this->t('See all results for %user_input', ['%user_input' => $user_input]));

    $suggestions[] = $suggestion;
  }

}
