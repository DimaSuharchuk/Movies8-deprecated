<?php

namespace Drupal\imdb;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Traversable;

/**
 * Provides an IMDbQueueManager plugin manager.
 */
class IMDbQueueManager extends DefaultPluginManager {

  /**
   * {@inheritdoc}
   */
  public function __construct(Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/IMDbQueue',
      $namespaces,
      $module_handler,
      'Drupal\imdb\IMDbQueuePluginInterface',
      'Drupal\imdb\Annotation\IMDbQueue'
    );

    $this->setCacheBackend($cache_backend, 'imdb_queue');
    $this->factory = new DefaultFactory($this->getDiscovery());
  }

}
