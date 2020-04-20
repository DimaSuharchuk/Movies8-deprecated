<?php

namespace Drupal\resource;

class CollectionParagraphSeason extends CollectionParagraph {

  /**
   * @param ParagraphSeason $paragraph
   *
   * @return Collection
   */
  public function add($paragraph): Collection {
    return parent::add($paragraph);
  }

}
