<?php

namespace Drupal\imdb\Plugin\IMDbQueue;

use DateTime;
use Drupal;
use Drupal\imdb\IMDbQueueItem;
use Drupal\imdb\IMDbQueuePluginBase;

/**
 * Class OMDbQueue.
 *
 * @IMDbQueue(
 *   id = "omdb_queue",
 *   limits = {
 *     "day" = "1000"
 *   }
 * )
 */
class OMDbQueue extends IMDbQueuePluginBase {

  /**
   * @inheritDoc
   */
  protected function prepareItem(IMDbQueueItem $itemData): IMDbQueueItem {
    // @todo It needs work.
    return $itemData;
  }

  /**
   * {@inheritDoc}
   */
  protected function refreshAvailability(): void {
    $refreshTime = $this->drupalState->get($this->availableCountRefreshTimestampVariableName, 0);
    // If "now" is greater than "refresh time" we set limit to maximum for today.
    if (Drupal::time()->getCurrentTime() > $refreshTime) {
      // Set refresh date as tomorrow 12am.
      $tomorrow = new DateTime('tomorrow');
      $this->drupalState->set($this->availableCountRefreshTimestampVariableName, $tomorrow->getTimestamp());
      // Refresh available count.
      $this->drupalState->set($this->availableCountVariableName, $this->getLimits()['day']);
    }

    $this->availableCount = $this->drupalState->get($this->availableCountVariableName);
  }

}
