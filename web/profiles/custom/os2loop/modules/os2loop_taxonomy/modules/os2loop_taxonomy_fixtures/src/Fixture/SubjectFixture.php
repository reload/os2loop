<?php

namespace Drupal\os2loop_taxonomy_fixtures\Fixture;

/**
 * Subject fixture.
 *
 * @package Drupal\os2loop_taxonomy_fixtures\Fixture
 */
class SubjectFixture extends TaxonomyTermFixture {
  /**
   * {@inheritdoc}
   */
  protected static $vocabularyId = 'os2loop_subject';

  /**
   * {@inheritdoc}
   */
  protected static $terms = [
    'food' => NULL,
    'it' => [
      'test' => NULL,
      'develop' => NULL,
    ],
    'a' => [
      'aa' => NULL,
      'aaa' => [
        'x' => NULL,
        'y' => NULL,
      ],
    ],
  ];

}
