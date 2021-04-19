<?php

namespace Drupal\os2loop_flag_content;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Drupal\Core\Url;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Custom twig functions.
 */
class FlagContent extends AbstractExtension {
  use StringTranslationTrait;

  /**
   * Returns the available functions.
   */
  public function getFunctions() {
    return [
      new TwigFunction('flag_form', [$this, 'flagForm']),
    ];
  }

  /**
   * Creates a flag form.
   */
  public function flagForm($node) {
    $nid = $node->id();
    $form['node'] = [
      '#type' => 'hidden',
      '#value' => $nid,
    ];

    $form['redirect_button'] = [
      '#type' => 'link',
      '#attributes' => [
        'class' => [
          'button',
        ],
      ],
      '#title' => $this->t('Contact editorial staff'),
      '#url' => new Url('os2loop_flag_content.flag_content_form', ['node' => $nid]),
    ];

    return $form;
  }

}
