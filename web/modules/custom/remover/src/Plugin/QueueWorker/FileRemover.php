<?php

namespace Drupal\remover\Plugin\QueueWorker;

use Drupal\remover\EntityRemover;

/**
 * Process a queue of removing files.
 *
 * @QueueWorker(
 *   id = "file_remover",
 *   title = @Translation("File remover"),
 *   cron = {"time" = 30}
 * )
 */
class FileRemover extends EntityRemover {

}
