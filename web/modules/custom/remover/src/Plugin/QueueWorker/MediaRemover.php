<?php

namespace Drupal\remover\Plugin\QueueWorker;

use Drupal\remover\EntityRemover;

/**
 * Process a queue of removing media entities.
 *
 * @QueueWorker(
 *   id = "media_remover",
 *   title = @Translation("Media remover"),
 *   cron = {"time" = 30}
 * )
 */
class MediaRemover extends EntityRemover {

}
