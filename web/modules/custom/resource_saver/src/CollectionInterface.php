<?php

namespace Drupal\resource_saver;

interface CollectionInterface {

  /**
   * Prevent creation collection with any arguments.
   */
  public function __construct();

  /**
   * Add resource in collection.
   *
   * @param \Drupal\resource_saver\ResourceInterface $resource
   *
   * @return \Drupal\resource_saver\Collection
   */
  public function add(ResourceInterface $resource): Collection;

}
