<?php

namespace Drupal\resource;

class ParagraphCast extends ParagraphCredits {

  protected $bundle = 'cast_person';

  /**
   * ParagraphCast constructor.
   *
   * @param string $field_character
   *   The name of the character in the movie.
   *
   * {@inheritDoc}
   */
  public function __construct(TaxonomyPerson $field_person, string $field_character) {
    parent::__construct($field_person);

    $this->fields['field_character'] = $field_character;
  }

}
