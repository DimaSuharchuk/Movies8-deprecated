<?php

namespace Drupal\resource;

class CollectionTaxonomyPerson extends CollectionTaxonomy {

  /**
   * @param TaxonomyPerson $term
   *
   * @return Collection
   */
  public function add($term): Collection {
    return parent::add($term);
  }

}
