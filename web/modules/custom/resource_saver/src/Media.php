<?php

namespace Drupal\resource_saver;

abstract class Media extends ResourceBase {

  protected $type = 'media';

  protected $bundle_key = 'bundle';

  /**
   * Media constructor.
   *
   * @param string|null $name
   *   Name of Media entity. Used in admin panel. Also can be use as unique
   *   field.
   *
   * {@inheritDoc}
   */
  public function __construct(?string $name) {
    parent::__construct();

    $this->fields['name'] = $name;
  }

}
