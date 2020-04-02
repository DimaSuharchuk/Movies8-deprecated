<?php

namespace Drupal\resource_saver;

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
