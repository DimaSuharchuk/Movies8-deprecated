<?php

namespace Drupal\resource;

abstract class CollectionMedia extends Collection {

  /**
   * @param Media $media
   *
   * @return Collection
   */
  public function add($media): Collection {
    return parent::add($media);
  }

}
