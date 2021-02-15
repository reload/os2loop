<?php

namespace Drupal\os2loop_page_fixtures\Fixture;

use Drupal\content_fixtures\Fixture\AbstractFixture;
use Drupal\content_fixtures\Fixture\DependentFixtureInterface;
use Drupal\content_fixtures\Fixture\FixtureGroupInterface;
use Drupal\node\Entity\Node;
use Drupal\os2loop_fixtures\Fixture\FileFixture;

/**
 * Page fixture.
 *
 * @package Drupal\os2loop_page_fixtures\Fixture
 */
class PageFixture extends AbstractFixture implements DependentFixtureInterface, FixtureGroupInterface {

  /**
   * {@inheritdoc}
   */
  public function load() {
    $page = Node::create([
      'type' => 'os2loop_page',
      'title' => 'The first page',
      'body' => [
        'value' => <<<'BODY'
This is the very first page!
BODY,
        'format' => 'os2loop_page',
      ],
      'field_os2loop_page_image' => [
        'target_id' => $this->getReference('file:image-001.jpg')->id(),
        'alt' => 'This is an image',
      ],
    ]);
    $page->save();
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies() {
    return [
      FileFixture::class,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getGroups() {
    return ['os2loop_page'];
  }

}
