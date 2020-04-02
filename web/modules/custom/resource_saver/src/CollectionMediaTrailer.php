<?php

namespace Drupal\resource_saver;

class CollectionMediaTrailer extends CollectionMedia {

  /**
   * @param MediaTrailer $media
   *
   * @return Collection
   */
  public function add($media): Collection {
    return parent::add($media);
  }

}
