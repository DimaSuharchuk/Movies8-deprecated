<?php

namespace Drupal\resource;

abstract class ParagraphCredits extends Paragraph {

  /**
   * ParagraphCredits constructor.
   *
   * @param \Drupal\resource\TaxonomyPerson $field_person
   *   Taxonomy entity of Person.
   *
   * {@inheritDoc}
   */
  public function __construct(TaxonomyPerson $field_person) {
    parent::__construct();

    $this->fields['field_person'] = $field_person;
  }

}
