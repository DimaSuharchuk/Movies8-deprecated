<?php

namespace Drupal\resource_saver;

abstract class Node extends ResourceBase {

  protected $type = 'node';

  protected $unique_field = 'field_tmdb_id';

  /**
   * Node constructor.
   *
   * @param string $title
   *   Node title.
   * @param int $field_tmdb_id
   *   Unique field contains TMDb ID of entity.
   *
   * {@inheritDoc}
   */
  public function __construct(string $title, int $field_tmdb_id) {
    parent::__construct();

    $this->fields['title'] = $title;
    $this->fields['field_tmdb_id'] = $field_tmdb_id;
  }

}
