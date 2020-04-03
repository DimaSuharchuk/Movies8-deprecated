<?php

namespace Drupal\imdb_saver\Plugin\QueueWorker;

use Drupal;
use Drupal\Core\Queue\QueueWorkerBase;

/**
 * Process a queue of TMDb result items to save on site.
 *
 * @QueueWorker(
 *   id = "tmdb_result_saver",
 *   title = @Translation("TMDb result saver"),
 *   cron = {"time" = 30}
 * )
 */
class TMDbSaver extends QueueWorkerBase {

  /**
   * @inheritDoc
   */
  public function processItem($data) {
    /** @var \Drupal\imdb_saver\IMDbSaverManager $imdb_saver_manager */
    $imdb_saver_manager = Drupal::service('plugin.manager.imdb_saver');
    if ($imdb_saver_manager->hasDefinition($data['plugin_id'])) {
      /** @var \Drupal\imdb_saver\IMDbSaverInterface $saver */
      $saver = $imdb_saver_manager->createInstance($data['plugin_id']);
      $saver->save($data['item']);
    }
  }

}
