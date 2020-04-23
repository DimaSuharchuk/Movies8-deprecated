<?php

namespace Drupal\remover\Plugin\QueueWorker;

use Drupal\remover\EntityRemover;

/**
 * Process a queue of removing nodes.
 *
 * @QueueWorker(
 *   id = "node_remover",
 *   title = @Translation("Node remover"),
 *   cron = {"time" = 30}
 * )
 */
class NodeRemover extends EntityRemover {

}
