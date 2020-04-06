<?php

namespace Drupal\resource;

use Drupal\Core\Entity\ContentEntityInterface;

interface CollectionDrupalEntityInterface {

  /**
   * Prevent creation collection with any arguments.
   */
  public function __construct();

  /**
   * Add entity in collection.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *
   * @return \Drupal\resource\CollectionDrupalEntity
   */
  public function add(ContentEntityInterface $entity): CollectionDrupalEntity;

}
