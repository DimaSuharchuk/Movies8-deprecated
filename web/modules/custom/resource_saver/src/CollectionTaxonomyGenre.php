<?php

namespace Drupal\resource_saver;

class CollectionTaxonomyGenre extends CollectionTaxonomy {

  /**
   * @param TaxonomyGenre $term
   *
   * @return Collection
   */
  public function add($term): Collection {
    return parent::add($term);
  }

}
