<?php

namespace Drupal\os2loop_taxonomy_fixtures\Fixture;

/**
 * Profession fixture.
 *
 * @package Drupal\os2loop_taxonomy_fixtures\Fixture
 */
class ProfessionFixture extends TaxonomyTermFixture {
  /**
   * {@inheritdoc}
   */
  protected static $vocabularyId = 'os2loop_profession';

  /**
   * {@inheritdoc}
   */
  protected static $terms = [
    'user',
    'editor',
  ];

}
