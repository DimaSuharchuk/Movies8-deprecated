<?php

namespace Drupal\resource;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\imdb\IMDbQueueItemLanguage;

interface ResourceInterface {

  /**
   * Set the language in which the entity will be saved.
   *
   * @param IMDbQueueItemLanguage $lang_object
   *
   * @return ResourceInterface
   */
  public function setLanguage(IMDbQueueItemLanguage $lang_object): ResourceInterface;

  /**
   * Recursively save entity and all included entities.
   *
   * @return ContentEntityInterface
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function save(): ContentEntityInterface;

}
