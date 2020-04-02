<?php

namespace Drupal\resource_saver;

class CollectionParagraphCrew extends CollectionParagraph {

  /**
   * @param ParagraphCrew $paragraph
   *
   * @return Collection
   */
  public function add($paragraph): Collection {
    return parent::add($paragraph);
  }

}
