<?php

namespace Drupal\resource;

use Drupal;
use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\imdb\IMDbQueueItemLanguage;

abstract class ResourceBase implements ResourceInterface {

  /**
   * Entity type from plugin definition.
   *
   * @var string
   */
  protected $type;

  /**
   * Bundle entity key from entity definition annotation.
   *
   * @var string
   */
  protected $bundle_key = 'type';

  /**
   * Entity bundle from plugin definition.
   *
   * @var string
   */
  protected $bundle;

  /**
   * Machine name of unique field of the entity using for search in storage.
   *
   * @var string
   */
  protected $unique_field;

  /**
   * Array of fields machine names for current entity.
   *
   * @var array
   */
  protected $fields = [];

  /**
   * @var EntityStorageInterface
   */
  protected $storage;

  /**
   * @var IMDbQueueItemLanguage
   */
  protected $lang_object;

  /**
   * @var string
   */
  protected $lang_code = IMDbQueueItemLanguage::ENG;

  /**
   * @var ContentEntityInterface
   */
  protected $entity = NULL;

  /**
   * We set language of child entities same as parent has, but some entities
   * should be created only on English.
   * To lock English language for entity (ResourceInterface) set this property
   * to TRUE.
   *
   * @var bool
   */
  protected $lock_eng_language = FALSE;


  /**
   * ResourceBase constructor.
   *
   * @throws PluginException
   */
  public function __construct() {
    $this->storage = Drupal::entityTypeManager()->getStorage($this->type);
    $this->lang_object = IMDbQueueItemLanguage::ENG();
  }

  /**
   * {@inheritDoc}
   */
  public function setLanguage(IMDbQueueItemLanguage $lang_object): ResourceInterface {
    $lang_object = $this->lock_eng_language ? IMDbQueueItemLanguage::ENG() : $lang_object;

    $this->lang_object = $lang_object;
    $this->lang_code = $lang_object->value();

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function save(): ContentEntityInterface {
    $this->entity = NULL;
    // This means translatable fields.
    // We shouldn't update fields when save some translation of entity.
    // Empty array means "all fields available to save data".
    $available_fields = [];

    // Find entity by unique field value, for example by TMDb ID.
    // We must not keep the same entity twice.
    if ($this->unique_field) {
      $finder = Drupal::service('entity_finder');
      $this->entity = $finder
        ->findEntities($this->type)
        ->byBundle($this->bundle)
        ->addCondition($this->unique_field, $this->fields[$this->unique_field])
        ->reduce()
        ->execute();
    }

    if ($this->entity) {
      if ($this->entity->hasTranslation($this->lang_code)) {
        // Prevent creating same entity.
        return $this->entity->getTranslation($this->lang_code);
      }
      else {
        $this->entity = $this->entity->addTranslation($this->lang_code);
        if (method_exists($this->entity, 'setOwnerId')) {
          $this->entity->setOwnerId(1);
        }

        $available_fields = array_keys($this->entity->getTranslatableFields());
      }
    }
    else {
      $this->entity = $this->newEntity();
    }

    // Save data in field of current entity.
    foreach ($this->fields as $field => $item) {
      // Empty array of "available field" is simple solution then get all field
      // and check for that "all fields" again.
      // All fields we using when save eng version of entity.
      // For other languages a lot of fields couldn't be updated. So we update
      // only translatable.
      if ($item && (!$available_fields || in_array($field, $available_fields))) {
        // Some fields may have many values (cardinality !== 1), we use iterable
        // collections and save every entity before adding in (multiple) field.
        if ($item instanceof Collection) {
          while ($item->valid()) {
            /** @var ResourceInterface $sub_item */
            $sub_item = $item->current();
            // Automatically set language to child entities and save them first.
            $sub_entity = $sub_item->setLanguage($this->lang_object)->save();
            // Save every child entity in this (parent) entity's field.
            $this->entity->{$field}->appendItem($sub_entity);
            // Move to next element in collection.
            $item->next();
          }
        }
        else {
          // "Child entity".
          if ($item instanceof ResourceInterface) {
            // Automatically set language to child entities and save them first.
            $item = $item->setLanguage($this->lang_object)->save();
          }
          // Just save data of primitive types.
          $this->entity->set($field, $item);
        }
      }
    }

    $this->preSave();
    $this->entity->save();

    return $this->entity;
  }

  /**
   * Create entity for type defined in child class with base configs.
   *
   * @return ContentEntityInterface
   */
  protected function newEntity(): ContentEntityInterface {
    /** @var ContentEntityInterface $entity */
    $entity = $this->storage->create([
      $this->bundle_key => $this->bundle,
      'uid' => 1,
    ]);

    return $entity;
  }

  /**
   * Do something with entity ($this->entity) before saving.
   */
  protected function preSave() {
  }

}
