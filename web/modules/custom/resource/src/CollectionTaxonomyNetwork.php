<?php

namespace Drupal\resource;

class CollectionTaxonomyNetwork extends CollectionTaxonomy {

  /**
   * @param TaxonomyNetwork $term
   *
   * @return Collection
   */
  public function add($term): Collection {
    return parent::add($term);
  }

}
