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
   * @param \Drupal\resource\CollectionParagraphCast $field_cast
   *   List of persons who starred in the movie.
   * @param \Drupal\resource\NodeCollection|null $field_collection
   *   Belongs to collection.
   * @param \Drupal\resource\CollectionParagraphCrew $field_crew
   *   List of persons who worked on movie.
   * @param \Drupal\resource\CollectionTaxonomyGenre $field_genres
   *   List genres this movie belongs to.
   * @param string $field_imdb_id
   *   IMDb ID, like tt1234567.
   * @param float $field_imdb_rating
   *   Movie rating from IMDb.
   * @param string $field_original_title
   *   The movie's title in English.
   * @param string $field_overview
   *   Movie overview.
   * @param \Drupal\resource\MediaPoster $field_poster
   *   Poster Media entity.
   * @param \Drupal\resource\CollectionMediaProductionCompany $field_production_companies
   *   Production companies created the movie.
   * @param \Drupal\resource\CollectionNodeMovie $field_recommendations
   *   List of same nodes like this. TMDb users recommend the movies in this
   *   list.
   * @param bool $field_recommended
   *   TRUE, if I recommend this movie.
   * @param \DateTime $field_release_date
   *   Date when the movie saw the world.
   * @param int $field_runtime
   *   Movie duration in minutes.
   * @param \Drupal\resource\CollectionNodeMovie $field_similar
   *   List of same nodes like this. TMDb algorithms recommend the movies in
   *   this list.
   * @param int $field_tmdb_id
   *   Unique field contains TMDb ID of entity.
   * @param \Drupal\resource\CollectionMediaTrailer $field_trailers
   *   Youtube Media entities.
   *
   * {@inheritDoc}
   */
  public function __construct(
    string $title,
    CollectionParagraphCast $field_cast,
    ?NodeCollection $field_collection,
    CollectionParagraphCrew $field_crew,
    CollectionTaxonomyGenre $field_genres,
    string $field_imdb_id,
    float $field_imdb_rating,
    string $field_original_title,
    string $field_overview,
    MediaPoster $field_poster,
    CollectionMediaProductionCompany $field_production_companies,
    CollectionNodeMovie $field_recommendations,
    bool $field_recommended,
    DateTime $field_release_date,
    int $field_runtime,
    CollectionNodeMovie $field_similar,
    int $field_tmdb_id,
    CollectionMediaTrailer $field_trailers
  ) {
    parent::__construct($title, $field_tmdb_id);

    $this->fields['field_cast'] = $field_cast;
    $this->fields['field_collection'] = $field_collection;
    $this->fields['field_crew'] = $field_crew;
    $this->fields['field_genres'] = $field_genres;
    $this->fields['field_imdb_id'] = $field_imdb_id;
    $this->fields['field_imdb_rating'] = $field_imdb_rating;
    $this->fields['field_original_title'] = $field_original_title;
    $this->fields['field_overview'] = $field_overview;
    $this->fields['field_poster'] = $field_poster;
    $this->fields['field_production_companies'] = $field_production_companies;
    $this->fields['field_recommendations'] = $field_recommendations;
    $this->fields['field_recommended'] = $field_recommended;
    $this->fields['field_release_date'] = $field_release_date ? $field_release_date->format('Y-m-d') : NULL;
    $this->fields['field_runtime'] = $field_runtime;
    $this->fields['field_similar'] = $field_similar;
    $this->fields['field_trailers'] = $field_trailers;
  }

}
