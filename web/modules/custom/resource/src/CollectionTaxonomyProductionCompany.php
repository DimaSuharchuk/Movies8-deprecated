<?php

namespace Drupal\resource;

class CollectionTaxonomyProductionCompany extends CollectionTaxonomy {

  /**
   * @param TaxonomyProductionCompany $term
   *
   * @return Collection
   */
  public function add($term): Collection {
    return parent::add($term);
  }

}
