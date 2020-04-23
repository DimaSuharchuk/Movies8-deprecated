<?php

namespace Drupal\remover\Plugin\QueueWorker;

use Drupal\remover\EntityRemover;

/**
 * Process a queue of removing taxonomy terms.
 *
 * @QueueWorker(
 *   id = "taxonomy_term_remover",
 *   title = @Translation("Taxonomy remover"),
 *   cron = {"time" = 30}
 * )
 */
class TaxonomyRemover extends EntityRemover {

}
