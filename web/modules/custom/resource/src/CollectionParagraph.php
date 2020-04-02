<?php

namespace Drupal\resource;

abstract class CollectionParagraph extends Collection {

  /**
   * @param Paragraph $paragraph
   *
   * @return Collection
   */
  public function add($paragraph): Collection {
    return parent::add($paragraph);
  }

}
