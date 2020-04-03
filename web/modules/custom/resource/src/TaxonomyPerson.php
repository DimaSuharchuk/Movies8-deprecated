<?php

namespace Drupal\resource;

class TaxonomyPerson extends Taxonomy {

  protected $bundle = 'person';

  protected $lock_eng_language = TRUE;

  /**
   * TaxonomyPerson constructor.
   *
   * @param \Drupal\resource\MediaProfile|null $field_profile
   *   Media entity with avatar of the person.
   *
   * {@inheritDoc}
   */
  public function __construct(string $name, int $field_tmdb_id, ?MediaProfile $field_profile) {
    parent::__construct($name, $field_tmdb_id);

    $this->fields['field_profile'] = !$field_profile ?: $field_profile->setAllNameAttributes($name);
  }

}
