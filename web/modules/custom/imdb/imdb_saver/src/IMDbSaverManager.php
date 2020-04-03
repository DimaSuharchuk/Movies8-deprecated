<?php

namespace Drupal\imdb_saver;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\imdb_saver\Annotation\IMDbSaver;
use Traversable;

/**
 * Manages all plugins to save data in entities.
 */
class IMDbSaverManager extends DefaultPluginManager {

  /**
   * Constructs a new IMDbSaverManager.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/IMDbSaver',
      $namespaces,
      $module_handler,
      IMDbSaverInterface::class,
      IMDbSaver::class
    );

    $this->setCacheBackend($cache_backend, 'imdb_saver');
  }

}
