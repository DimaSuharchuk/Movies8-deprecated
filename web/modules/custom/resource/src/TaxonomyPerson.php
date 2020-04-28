<?php

namespace Drupal\resource;

class TaxonomyPerson extends Taxonomy {

  protected $bundle = 'person';

  protected $lock_eng_language = TRUE;

  /**
   * TaxonomyPerson constructor.
   *
   * @param string $field_profile_path
   *   Avatar of the person.
   *
   * {@inheritDoc}
   */
  public function __construct(string $name, int $field_tmdb_id, ?string $field_profile_path) {
    parent::__construct($name, $field_tmdb_id);

    $this->fields['field_profile_path'] = $field_profile_path;
  }

}
