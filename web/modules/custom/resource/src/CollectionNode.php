<?php

namespace Drupal\resource;

abstract class CollectionNode extends Collection {

  /**
   * @param Node $node
   *
   * @return Collection
   */
  public function add($node): Collection {
    return parent::add($node);
  }

}
