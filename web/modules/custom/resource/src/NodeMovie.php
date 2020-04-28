<?php

namespace Drupal\resource;

use DateTime;

class NodeMovie extends Node {

  protected $bundle = 'movie';

  /**
   * NodeMovie constructor.
   *
   * @param string $title
   *   Node title.
   * @param bool $field_approved
   *   TRUE, if I approve this movie.
   * @param \Drupal\resource\CollectionParagraphCast $field_cast
   *   List of persons who starred in the movie.
   * @param \Drupal\resource\TaxonomyMovieCollection|null $field_collection
   *   Belongs to collection.
   * @param \Drupal\resource\CollectionParagraphCrew $field_crew
   *   List of persons who worked on movie.
   * @param \Drupal\resource\CollectionTaxonomyGenre $field_genres
   *   List genres this movie belongs to.
   * @param string $field_imdb_id
   *   IMDb ID, like tt12345678.
   * @param string $field_original_title
   *   The movie's title in English.
   * @param string $field_overview
   *   Movie overview.
   * @param string $field_poster_path
   *   Poster of movie.
   * @param \Drupal\resource\CollectionTaxonomyProductionCompany $field_production_companies
   *   Production companies created the movie.
   * @param \DateTime $field_release_date
   *   Date when the movie saw the world.
   * @param int $field_runtime
   *   Movie duration in minutes.
   * @param string $field_site
   *   Official web page or site of the movie.
   * @param int $field_tmdb_id
   *   Unique field contains TMDb ID of entity.
   * @param \Drupal\resource\CollectionMediaTrailer $field_trailers
   *   Trailers of the movie from Youtube.
   *
   * {@inheritDoc}
   */
  public function __construct(
    string $title,
    bool $field_approved,
    CollectionParagraphCast $field_cast,
    ?TaxonomyMovieCollection $field_collection,
    CollectionParagraphCrew $field_crew,
    CollectionTaxonomyGenre $field_genres,
    string $field_imdb_id,
    string $field_original_title,
    string $field_overview,
    string $field_poster_path,
    CollectionTaxonomyProductionCompany $field_production_companies,
    DateTime $field_release_date,
    int $field_runtime,
    string $field_site,
    int $field_tmdb_id,
    CollectionMediaTrailer $field_trailers
  ) {
    parent::__construct($title, $field_tmdb_id);

    $this->fields['field_approved'] = $field_approved;
    $this->fields['field_cast'] = $field_cast;
    $this->fields['field_collection'] = $field_collection;
    $this->fields['field_crew'] = $field_crew;
    $this->fields['field_genres'] = $field_genres;
    $this->fields['field_imdb_id'] = $field_imdb_id;
    $this->fields['field_original_title'] = $field_original_title;
    $this->fields['field_overview'] = $field_overview;
    $this->fields['field_poster_path'] = $field_poster_path;
    $this->fields['field_production_companies'] = $field_production_companies;
    $this->fields['field_release_date'] = $field_release_date ? $field_release_date->format('Y-m-d') : NULL;
    $this->fields['field_runtime'] = $field_runtime;
    $this->fields['field_site'] = $field_site;
    $this->fields['field_trailers'] = $field_trailers;
  }

}
