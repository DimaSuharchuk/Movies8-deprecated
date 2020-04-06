<?php

namespace Drupal\resource;

use Drupal\node\NodeInterface;

class CollectionDrupalEntityNode extends CollectionDrupalEntity {

  /**
   * @param NodeInterface $node
   *
   * @return \Drupal\resource\CollectionDrupalEntity
   */
  public function add($node): CollectionDrupalEntity {
    return parent::add($node);
  }

}
