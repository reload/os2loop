<?php

namespace Drupal\os2loop_documents\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\RendererInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Entity print controller.
 */
class EntityPrintController extends ControllerBase {
  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  private $renderer;

  /**
   * {@inheritdoc}
   */
  public function __construct(RendererInterface $renderer) {
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('renderer')
    );
  }

  /**
   * Render a region.
   */
  public function region(NodeInterface $node, string $region) {
    $build[] = [
      '#theme' => 'os2loop_documents_pdf_' . $region,
      '#node' => $node,
    ];

    $response = new Response();
    $response->setContent($this->renderer->renderRoot($build));

    return $response;
  }

}
