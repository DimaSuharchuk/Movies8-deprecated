<?php

namespace Drupal\resource;

class NodeTv extends Node {

  protected $bundle = 'tv';

  /**
   * NodeTv constructor.
   *
   * @param string $title
   *   Node title.
   * @param bool $field_approved
   *   TRUE, if I approve this movie.
   * @param CollectionParagraphCast $field_cast
   *   List of main persons who starred in the TV.
   * @param CollectionTaxonomyPerson $field_created_by
   *   List of show runners of the TV.
   * @param CollectionParagraphCrew $field_crew
   *   List of persons who worked on TV.
   * @param CollectionTaxonomyGenre $field_genres
   *   List genres this TV belongs to.
   * @param string $field_imdb_id
   *   IMDb ID, like tt12345678.
   * @param bool $field_in_production
   *   Is the TV finished or in production. TRUE = "in production" yet.
   * @param CollectionMediaNetwork $field_networks
   *   List of channels TV belongs to.
   * @param int $field_number_of_episodes
   *   Number of all episodes in the TV.
   * @param int $field_number_of_seasons
   *   Number of all seasons in the TV.
   * @param string $field_original_title
   *   The TV's title in English.
   * @param string $field_overview
   *   TV show overview.
   * @param MediaPoster|null $field_poster
   *   Poster of TV.
   * @param CollectionMediaProductionCompany $field_production_companies
   *   Production companies created the movie.
   * @param CollectionParagraphSeason $field_seasons
   *   Reference field of paragraphs "Season" entities.
   * @param int $field_runtime
   *   Average number of episodes runtime.
   * @param string $field_site
   *   Official web page or site of the TV.
   * @param int $field_tmdb_id
   *   Unique field contains TMDb ID of entity.
   * @param CollectionMediaTrailer $field_trailers
   *   Trailers of the TV from Youtube.
   *
   * {@inheritDoc}
   */
  public function __construct(
    string $title,
    bool $field_approved,
    CollectionParagraphCast $field_cast,
    CollectionTaxonomyPerson $field_created_by,
    CollectionParagraphCrew $field_crew,
    CollectionTaxonomyGenre $field_genres,
    string $field_imdb_id,
    bool $field_in_production,
    CollectionMediaNetwork $field_networks,
    int $field_number_of_episodes,
    int $field_number_of_seasons,
    string $field_original_title,
    string $field_overview,
    ?MediaPoster $field_poster,
    CollectionMediaProductionCompany $field_production_companies,
    CollectionParagraphSeason $field_seasons,
    int $field_runtime,
    string $field_site,
    int $field_tmdb_id,
    CollectionMediaTrailer $field_trailers
  ) {
    parent::__construct($title, $field_tmdb_id);

    $this->fields['field_approved'] = $field_approved;
    $this->fields['field_cast'] = $field_cast;
    $this->fields['field_created_by'] = $field_created_by;
    $this->fields['field_crew'] = $field_crew;
    $this->fields['field_genres'] = $field_genres;
    $this->fields['field_imdb_id'] = $field_imdb_id;
    $this->fields['field_in_production'] = $field_in_production;
    $this->fields['field_networks'] = $field_networks;
    $this->fields['field_number_of_episodes'] = $field_number_of_episodes;
    $this->fields['field_number_of_seasons'] = $field_number_of_seasons;
    $this->fields['field_original_title'] = $field_original_title;
    $this->fields['field_overview'] = $field_overview;
    $this->fields['field_poster'] = $field_poster;
    $this->fields['field_production_companies'] = $field_production_companies;
    $this->fields['field_seasons'] = $field_seasons;
    $this->fields['field_runtime'] = $field_runtime;
    $this->fields['field_site'] = $field_site;
    $this->fields['field_trailers'] = $field_trailers;
  }

}
