<?php

namespace Drupal\resource;

class MediaProductionCompany extends MediaImage {

  protected $bundle = 'production_company';

  protected $unique_field = 'field_tmdb_id';

  /**
   * MediaProductionCompany constructor.
   *
   * @param int $field_tmdb_id
   *   Just field TMDb ID used everywhere in the site. Also it's unique field.
   *
   * {@inheritDoc}
   */
  public function __construct(int $field_tmdb_id, FileImage $field_media_image, string $name = NULL, string $alt = NULL, string $title = NULL) {
    parent::__construct($field_media_image, $name, $alt, $title);

    $this->fields['field_tmdb_id'] = $field_tmdb_id;
  }

}
