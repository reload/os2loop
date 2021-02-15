<?php

namespace Drupal\os2loop_taxonomy_fixtures\Fixture;

/**
 * Tag fixture.
 *
 * @package Drupal\os2loop_taxonomy_fixtures\Fixture
 */
class TagFixture extends TaxonomyTermFixture {
  /**
   * {@inheritdoc}
   */
  protected static $vocabularyId = 'os2loop_tag';

  /**
   * {@inheritdoc}
   */
  protected static $terms = [
    'hat',
    'briller',
  ];

}
