<?php

namespace Drupal\resource_saver;

abstract class CollectionTaxonomy extends Collection {

  /**
   * @param Taxonomy $term
   *
   * @return Collection
   */
  public function add($term): Collection {
    return parent::add($term);
  }

}
