<?php

namespace Drupal\resource;

class CollectionMediaProductionCompany extends CollectionMedia {

  /**
   * @param MediaProductionCompany $media
   *
   * @return Collection
   */
  public function add($media): Collection {
    return parent::add($media);
  }

}
