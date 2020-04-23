<?php

namespace Drupal\remover\Plugin\QueueWorker;

use Drupal\remover\EntityRemover;

/**
 * Process a queue of removing paragraph entities.
 *
 * @QueueWorker(
 *   id = "paragraph_remover",
 *   title = @Translation("Paragraph remover"),
 *   cron = {"time" = 30}
 * )
 */
class ParagraphRemover extends EntityRemover {

}
