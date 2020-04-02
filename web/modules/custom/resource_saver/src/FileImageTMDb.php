<?php

namespace Drupal\resource_saver;

class FileImageTMDb extends FileImage {

  const TMDb_IMAGE_ORIGINAL_BASE_PATH = 'https://image.tmdb.org/t/p/original';

  /**
   * {@inheritDoc}
   */
  public function __construct(string $remote_image_path, string $directory = NULL) {
    parent::__construct(self::TMDb_IMAGE_ORIGINAL_BASE_PATH . $remote_image_path, $directory);
  }

}
