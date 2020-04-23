<?php

namespace Drupal\remover;

use Drupal;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;

abstract class EntityRemover extends QueueWorkerBase {

  use StringTranslationTrait;

  /**
   * {@inheritDoc}
   */
  public function processItem($data) {
    $finder = Drupal::service('entity_finder');
    // Load entities for next multiple deleting.
    $entities = $finder
      ->findEntities($data['type'])
      ->loadMultipleById($data['ids']);
    // Get storage of the type and remove entities.
    $storage = Drupal::entityTypeManager()->getStorage($data['type']);
    $storage->delete($entities);
  }

}
