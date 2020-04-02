<?php

namespace Drupal\resource_saver;

class TaxonomyPerson extends Taxonomy {

  protected $bundle = 'person';

  /**
   * TaxonomyPerson constructor.
   *
   * @param \Drupal\resource_saver\MediaProfile|null $field_profile
   *   Media entity with avatar of the person.
   *
   * {@inheritDoc}
   */
  public function __construct(string $name, int $field_tmdb_id, ?MediaProfile $field_profile) {
    parent::__construct($name, $field_tmdb_id);

    $this->fields['field_profile'] = !$field_profile ?: $field_profile->setAllNameAttributes($name);
  }

}
