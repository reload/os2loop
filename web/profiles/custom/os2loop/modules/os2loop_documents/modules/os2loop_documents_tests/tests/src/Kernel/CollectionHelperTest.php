<?php

namespace Drupal\Tests\os2loop_documents_tests\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\os2loop_documents\Entity\DocumentCollectionItem;
use Drupal\os2loop_documents\Helper\CollectionHelper;

/**
 * Collection helper test.
 *
 * @group os2loop_tests
 * @group os2loop_documents_tests
 */
class CollectionHelperTest extends KernelTestBase {
  /**
   * The collection helper.
   *
   * @var \Drupal\os2loop_documents\Helper\CollectionHelper
   */
  private $collectionHelper;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'os2loop_documents',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->collectionHelper = \Drupal::service(CollectionHelper::class);
  }

  /**
   * Test build tree.
   *
   * @dataProvider buildTreeDataProvider
   */
  public function testBuildTree(array $items, array $expected) {
    $actual = $this->collectionHelper->buildTree($items);
    $this->assertEquals($expected, $actual);
  }

  /**
   * Data provider.
   *
   * @return array
   *   List of [$items, $expected].
   */
  public function buildTreeDataProvider(): array {
    return [

      [
        [],
        [],
      ],

      [
        [
          DocumentCollectionItem::create([
            'id' => 1,
          ]),
        ],
        [],
      ],

    ];
  }

}
