<?php

namespace Drupal\resource_saver;

class TaxonomyGenre extends Taxonomy {

  protected $bundle = 'genre';

  /**
   * TaxonomyGenre constructor.
   *
   * {@inheritDoc}
   */
  public function __construct(string $name, int $field_tmdb_id) {
    $name = mb_convert_case($name, MB_CASE_TITLE);
    parent::__construct($name, $field_tmdb_id);
  }

}
