<?php

namespace Drupal\resource_saver;

class CollectionParagraphCast extends CollectionParagraph {

  /**
   * @param ParagraphCast $paragraph
   *
   * @return Collection
   */
  public function add($paragraph): Collection {
    return parent::add($paragraph);
  }

}
