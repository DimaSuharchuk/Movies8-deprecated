<?php

namespace Drupal\resource;

class ParagraphSeason extends Paragraph {

  protected $bundle = 'season';

  protected $unique_field = 'field_tmdb_id';

  /**
   * ParagraphSeason constructor.
   *
   * @param int $field_episode_count
   *   Count of episodes in season.
   * @param string $field_overview
   *   Season overview.
   * @param string $field_poster
   *   Poster of season.
   * @param int $field_season_number
   *   Number of TV season.
   * @param string $field_title
   *   Season's name.
   * @param int $field_tmdb_id
   *   TMDb ID.
   *
   * {@inheritDoc}
   */
  public function __construct(
    int $field_episode_count,
    string $field_overview,
    ?string $field_poster_path,
    int $field_season_number,
    string $field_title,
    int $field_tmdb_id
  ) {
    parent::__construct();

    $this->fields['field_episode_count'] = $field_episode_count;
    $this->fields['field_overview'] = $field_overview;
    $this->fields['field_poster_path'] = $field_poster_path;
    $this->fields['field_season_number'] = $field_season_number;
    $this->fields['field_title'] = $field_title;
    $this->fields['field_tmdb_id'] = $field_tmdb_id;
  }

}
