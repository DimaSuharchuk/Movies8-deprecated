<?php

namespace Drupal\resource;

class TaxonomyNetwork extends Taxonomy {

  protected $bundle = 'network';

  protected $lock_eng_language = TRUE;

  /**
   * TaxonomyProductionCompany constructor.
   *
   * @param string $field_logo_path
   *   Logo of network.
   *
   * {@inheritDoc}
   */
  public function __construct(string $name, int $field_tmdb_id, string $field_logo_path) {
    parent::__construct($name, $field_tmdb_id);

    $this->fields['field_logo_path'] = $field_logo_path;
  }

}
