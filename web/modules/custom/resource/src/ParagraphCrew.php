<?php

namespace Drupal\resource;

class ParagraphCrew extends ParagraphCredits {

  protected $bundle = 'crew_person';

  /**
   * ParagraphCrew constructor.
   *
   * @param string $field_department
   *   A film crew is divided into different departments, each of which
   *   specializes in a specific aspect of the production.
   * @param string $field_job
   *   A specific role in a particular department.
   *
   * {@inheritDoc}
   */
  public function __construct(TaxonomyPerson $field_person, string $field_department, string $field_job) {
    parent::__construct($field_person);

    $this->fields['field_department'] = $field_department;
    $this->fields['field_job'] = $field_job;
  }

}
