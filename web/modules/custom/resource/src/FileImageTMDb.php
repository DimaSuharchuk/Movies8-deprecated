<?php

namespace Drupal\resource;

class FileImageTMDb extends FileImage {

  const TMDb_IMAGE_ORIGINAL_BASE_PATH = 'https://image.tmdb.org/t/p/original';

  const TMDb_IMAGE_COMPRESSED_BASE_PATH = 'https://image.tmdb.org/t/p/w';

  /**
   * {@inheritDoc}
   */
  public function __construct(string $remote_image_path, int $width = NULL, string $directory = NULL) {
    $image_path = $width ? self::TMDb_IMAGE_COMPRESSED_BASE_PATH . $width . $remote_image_path : self::TMDb_IMAGE_ORIGINAL_BASE_PATH . $remote_image_path;
    parent::__construct($image_path, $directory);
  }

}
