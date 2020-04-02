<?php

namespace Drupal\resource;

interface CollectionInterface {

  /**
   * Prevent creation collection with any arguments.
   */
  public function __construct();

  /**
   * Add resource in collection.
   *
   * @param \Drupal\resource\ResourceInterface $resource
   *
   * @return \Drupal\resource\Collection
   */
  public function add(ResourceInterface $resource): Collection;

}
