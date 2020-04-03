<?php

namespace Drupal\imdb;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\imdb\Annotation\IMDbQueue;
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
      IMDbQueuePluginInterface::class,
      IMDbQueue::class
    );

    $this->setCacheBackend($cache_backend, 'imdb_queue');
  }

}
