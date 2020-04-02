<?php

namespace Drupal\resource;

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
