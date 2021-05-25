<?php

namespace Drupal\os2loop_flag_content\Services;

use Drupal\Core\Config\ConfigFactory;

/**
 * Service for flag contet admin config.
 *
 * @package Drupal\os2loop_flag_content\Services
 */
class ConfigService {

  /**
   * Configuration Factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Constructor.
   */
  public function __construct(ConfigFactory $configFactory) {
    $this->configFactory = $configFactory;
  }

  /**
   * Gets flag content setting.
   */
  public function getFlagContentSettings() {
    $config = $this->configFactory->get('os2loop_flag_content.settings');
    return $config;
  }

}
