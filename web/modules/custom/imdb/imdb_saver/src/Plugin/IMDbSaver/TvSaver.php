<?php

namespace Drupal\imdb_saver\Plugin\IMDbSaver;

use Drupal\imdb\IMDbQueueItem;
use Drupal\imdb_saver\IMDbSaverPluginBase;

/**
 * Save TMDb result in site entities.
 *
 * @IMDbSaver(
 *   id = "tv_saver"
 * )
 */
class TvSaver extends IMDbSaverPluginBase {

  /**
   * {@inheritDoc}
   */
  public function save(IMDbQueueItem $item): void {
    // @todo Add Resources first.
    \Drupal::logger('TvSaver')->debug('TV saving...');
  }

}
