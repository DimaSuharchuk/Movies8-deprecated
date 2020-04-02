<?php

namespace Drupal\resource_saver;

abstract class Taxonomy extends ResourceBase {

  protected $type = 'taxonomy_term';

  protected $bundle_key = 'vid';

  protected $unique_field = 'field_tmdb_id';

  /**
   * Taxonomy constructor.
   *
   * @param string $name
   *   Title field for any taxonomy term.
   * @param int $field_tmdb_id
   *   TMDb ID.
   *
   * {@inheritDoc}
   */
  public function __construct(string $name, int $field_tmdb_id) {
    parent::__construct();

    $this->fields['name'] = $name;
    $this->fields['field_tmdb_id'] = $field_tmdb_id;
  }

}
