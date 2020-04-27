<?php

namespace Drupal\imdb\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * @FieldFormatter(
 *   id = "tmdb_image_original",
 *   label = @Translation("Original"),
 *   field_types = {
 *     "tmdb_image",
 *   }
 * )
 */
class TmdbImageOriginal extends FormatterBase {

  public function settingsSummary() {
    return [
      $this->t('Rendered <em>Original</em> image.'),
    ];
  }

  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      if ($item->value) {
        $elements[$delta] = [
          '#theme' => 'image',
          '#uri' => 'https://image.tmdb.org/t/p/original' . $item->value,
          '#langcode' => $langcode,
        ];
      }
    }

    return $elements;
  }

}
