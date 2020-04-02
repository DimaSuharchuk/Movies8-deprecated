<?php

namespace Drupal\resource;

use Drupal;
use Drupal\Core\Entity\EntityFieldManagerInterface;

abstract class MediaImage extends Media {

  /**
   * Image's alternative text.
   *
   * @var string
   */
  private $alt;

  /**
   * Image's title.
   *
   * @var string
   */
  private $title;

  /**
   * MediaImage constructor.
   *
   * @param \Drupal\resource\FileImage $field_media_image
   *   File entity (image) should be saved in the Media entity.
   * @param string|null $alt
   *   Image's alternative text.
   * @param string|null $title
   *   Image's title.
   *
   * {@inheritDoc}
   */
  public function __construct(FileImage $field_media_image, string $name = NULL, string $alt = NULL, string $title = NULL) {
    parent::__construct($name);

    // Get directory image should be saved from image field configs.
    /** @var EntityFieldManagerInterface $entity_field_manager */
    $entity_field_manager = Drupal::service('entity_field.manager');
    $fields_definitions = $entity_field_manager->getFieldDefinitions($this->type, $this->bundle);
    $directory = $fields_definitions['field_media_image']->getSetting('file_directory');

    $this->fields['field_media_image'] = $field_media_image->setDirectory($directory);
    $this->alt = $alt;
    $this->title = $title;
  }

  public function setAllNameAttributes(string $name): self {
    $this->fields['name'] = $name;
    $this->alt = $name;
    $this->title = $name;
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  protected function preSave() {
    $this->entity->{'field_media_image'}->alt = $this->alt;
    $this->entity->{'field_media_image'}->title = $this->title;
  }

}
