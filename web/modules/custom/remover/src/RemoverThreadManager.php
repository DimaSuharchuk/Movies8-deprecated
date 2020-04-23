<?php

namespace Drupal\remover;

use Drupal;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class RemoverThreadManager {

  use StringTranslationTrait;

  private $limit = 100;

  /**
   * The method manages the queues for deleting entities.
   *
   * @param string $type
   *   Entity type, like "node" or "media".
   * @param array $ids
   *   Entities IDs.
   */
  public function process(string $type, array $ids) {
    // Check has the QueueWorker manager needed "remover" worker.
    $queue_manager = Drupal::service('plugin.manager.queue_worker');
    $worker_plugin_id = "{$type}_remover";
    if ($queue_manager->hasDefinition($worker_plugin_id)) {
      // Divide array of entities ids to chunks and create queue items for
      // entities multiple deletion.
      foreach (array_chunk($ids, $this->limit) as $chunk) {
        Drupal::queue($worker_plugin_id)->createItem([
          'type' => $type,
          'ids' => $chunk,
        ]);
      }
    }
    else {
      $message = $this->t('Queue worker with ID %id does not exists. Please add.', [
        '%id' => $worker_plugin_id,
      ]);
      Drupal::messenger()->addError($message);
      Drupal::logger(__CLASS__)->error($message);
    }
  }

  /**
   * Set a limit for simultaneous deletion of entities. Too large limit may not
   * fit in the specified 30 seconds, and too small may slow down the removal
   * process.
   *
   * @param int $limit
   */
  public function setLimit(int $limit) {
    $this->limit = $limit;
  }

}
