<?php

namespace Drupal\resource;

class MediaTrailer extends Media {

  protected $bundle = 'trailer';

  protected $unique_field = 'field_media_oembed_video';

  /**
   * MediaTrailer constructor.
   *
   * @param int $size
   *   Youtube video quality.
   * @param string $field_media_oembed_video
   *   Youtube video key.
   *
   * {@inheritDoc}
   */
  public function __construct(string $name, int $size, string $field_media_oembed_video) {
    parent::__construct($name);

    $this->fields['field_size'] = $size;
    $this->fields['field_media_oembed_video'] = 'https://www.youtube.com/watch?v=' . $field_media_oembed_video;
  }

}
