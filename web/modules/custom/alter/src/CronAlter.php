<?php

namespace Drupal\alter;

use Drupal\Core\Cron;
use Drupal\Core\CronInterface;

/**
 * Class CronAlter.
 *
 * @package Drupal\alter
 */
class CronAlter extends Cron implements CronInterface {

  /**
   * {@inheritDoc}
   */
  protected function setCronLastTime() {
    // Record cron time.
    $request_time = $this->time->getRequestTime();
    $this->state->set('system.cron_last', $request_time);
    // Removed logs from this place in Core.
  }

}
