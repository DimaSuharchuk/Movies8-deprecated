<?php

namespace Drupal\imdb_saver;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\imdb\IMDbQueueItem;

interface IMDbSaverInterface extends PluginInspectionInterface {

  /**
   * Save IMDbQueueItem into node and children entities.
   *
   * @param \Drupal\imdb\IMDbQueueItem $item
   *   Result fetched from TMDb API.
   */
  public function save(IMDbQueueItem $item): void;

}
