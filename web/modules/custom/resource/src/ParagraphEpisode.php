<?php

namespace Drupal\resource;

use DateTime;

class ParagraphEpisode extends Paragraph {

  protected $bundle = 'episode';

  protected $unique_field = 'field_tmdb_id';

  /**
   * ParagraphEpisode constructor.
   *
   * @param DateTime $field_air_date
   *   Episode air date.
   * @param int $field_episode_number
   *   Number of episode in season.
   * @param CollectionParagraphCast $field_guest_stars
   *   Guest stars who starred in the episode.
   * @param MediaEpisodeImage $field_image
   *   Image like a poster for episode.
   * @param string $field_overview
   *   Episode overview.
   * @param string $field_title
   *   Name of episode.
   * @param int $field_tmdb_id
   *   TMDb ID.
   *
   * {@inheritDoc}
   */
  public function __construct(
    DateTime $field_air_date,
    int $field_episode_number,
    CollectionParagraphCast $field_guest_stars,
    ?MediaEpisodeImage $field_image,
    string $field_overview,
    string $field_title,
    int $field_tmdb_id
  ) {
    parent::__construct();

    $this->fields['field_air_date'] = $field_air_date ? $field_air_date->format('Y-m-d') : NULL;
    $this->fields['field_episode_number'] = $field_episode_number;
    $this->fields['field_guest_stars'] = $field_guest_stars;
    $this->fields['field_image'] = $field_image;
    $this->fields['field_overview'] = $field_overview;
    $this->fields['field_title'] = $field_title;
    $this->fields['field_tmdb_id'] = $field_tmdb_id;
  }

}
