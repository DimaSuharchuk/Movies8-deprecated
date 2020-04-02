<?php

namespace Drupal\resource;

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
