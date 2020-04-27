<?php

namespace Drupal\imdb\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * @FieldType(
 *   id = "tmdb_image",
 *   label = @Translation("TMDb image"),
 *   description = @Translation("This field stores a name of TMDb image."),
 *   category = @Translation("Text"),
 *   default_widget = "tmdb_image_textfield",
 *   default_formatter = "tmdb_image_original"
 * )
 */
class TmdbImageItem extends FieldItemBase {

  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    return [
      'value' => DataDefinition::create('string')->setLabel(t('Text')),
    ];
  }

  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'varchar',
          'length' => 64,
        ],
      ],
    ];
  }

}
