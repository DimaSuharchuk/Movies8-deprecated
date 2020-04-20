<?php

namespace Drupal\resource;

class CollectionMediaNetwork extends CollectionMedia {

  /**
   * @param MediaNetwork $media
   *
   * @return Collection
   */
  public function add($media): Collection {
    return parent::add($media);
  }

}
