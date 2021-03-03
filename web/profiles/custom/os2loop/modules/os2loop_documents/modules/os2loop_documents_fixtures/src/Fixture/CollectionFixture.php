<?php

namespace Drupal\os2loop_documents_fixtures\Fixture;

use Drupal\content_fixtures\Fixture\AbstractFixture;
use Drupal\content_fixtures\Fixture\DependentFixtureInterface;
use Drupal\content_fixtures\Fixture\FixtureGroupInterface;
use Drupal\node\Entity\Node;
use Drupal\os2loop_taxonomy_fixtures\Fixture\ProfessionFixture;
use Drupal\os2loop_taxonomy_fixtures\Fixture\SubjectFixture;
use Drupal\os2loop_taxonomy_fixtures\Fixture\TagFixture;

/**
 * Page fixture.
 *
 * @package Drupal\os2loop_page_fixtures\Fixture
 */
class CollectionFixture extends AbstractFixture implements DependentFixtureInterface, FixtureGroupInterface {

  /**
   * {@inheritdoc}
   */
  public function load() {
    $document = Node::create([
      'type' => 'os2loop_documents_collection',
      'title' => 'The first collection',
      'os2loop_documents_dc_content' => [
        'value' => <<<'BODY'
This is the first collection of documents.
BODY,
        'format' => 'os2loop_documents_rich_text',
      ],
      'os2loop_shared_subject' => [
        'target_id' => $this->getReference('os2loop_subject:Diverse')->id(),
      ],
      'os2loop_shared_tags' => [
        ['target_id' => $this->getReference('os2loop_tag:test')->id()],
        ['target_id' => $this->getReference('os2loop_tag:Udredning')->id()],
      ],
      'os2loop_shared_profession' => [
        'target_id' => $this->getReference('os2loop_profession:Andet')->id(),
      ],
    ]);
    $document->save();
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies() {
    return [
      SubjectFixture::class,
      TagFixture::class,
      ProfessionFixture::class,
      DocumentFixture::class,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getGroups() {
    return ['os2loop_documents'];
  }

}
