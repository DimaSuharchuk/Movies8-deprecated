<?php

namespace Drupal\imdb\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @FieldFormatter(
 *   id = "tmdb_image_compact",
 *   label = @Translation("Compact"),
 *   field_types = {
 *     "tmdb_image",
 *   }
 * )
 */
class TmdbImageCompact extends FormatterBase {

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['width'] = [
      '#type' => 'select',
      '#options' => [
        200 => 200,
        300 => 300,
        400 => 400,
        500 => 500,
      ],
      '#default_value' => $this->getSetting('width'),
    ];

    return $elements;
  }

  public function settingsSummary() {
    return [
      $this->t('Rendered with width: %w', ['%w' => $this->getSetting('width')]),
    ];
  }

  public static function defaultSettings() {
    return ['width' => 400] + parent::defaultSettings();
  }

  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      if ($item->value) {
        $elements[$delta] = [
          '#theme' => 'image',
          '#uri' => 'https://image.tmdb.org/t/p/w' . $this->getSetting('width') . '/' . $item->value,
          '#langcode' => $langcode,
        ];
      }
    }

    return $elements;
  }

}
