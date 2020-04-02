<?php

namespace Drupal\resource_saver;

class CollectionNodeMovie extends CollectionNode {

  /**
   * @param NodeMovie $node
   *
   * @return Collection
   */
  public function add($node): Collection {
    return parent::add($node);
  }

}
