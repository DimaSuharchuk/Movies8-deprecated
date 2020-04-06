<?php

namespace Drupal\imdb_saver\Plugin\IMDbSaver;

use DateTime;
use Drupal;
use Drupal\imdb\IMDbQueueItem;
use Drupal\imdb\IMDbQueueItemLanguage;
use Drupal\imdb_saver\IMDbSaverPluginBase;
use Drupal\resource\CollectionMediaProductionCompany;
use Drupal\resource\CollectionMediaTrailer;
use Drupal\resource\CollectionParagraphCast;
use Drupal\resource\CollectionParagraphCrew;
use Drupal\resource\CollectionTaxonomyGenre;
use Drupal\resource\FileImageTMDb;
use Drupal\resource\MediaPoster;
use Drupal\resource\MediaProductionCompany;
use Drupal\resource\MediaProfile;
use Drupal\resource\MediaTrailer;
use Drupal\resource\NodeCollection;
use Drupal\resource\NodeMovie;
use Drupal\resource\ParagraphCast;
use Drupal\resource\ParagraphCrew;
use Drupal\resource\TaxonomyGenre;
use Drupal\resource\TaxonomyPerson;
use Exception;

/**
 * Save TMDb result in site entities.
 *
 * @IMDbSaver(
 *   id = "movie_saver"
 * )
 */
class MovieSaver extends IMDbSaverPluginBase {

  /**
   * {@inheritDoc}
   */
  public function save(IMDbQueueItem $item): void {
    $fields = $item->getFieldsData();

    // Simple fields.
    $title = $fields['title'];
    $field_imdb_id = $fields['imdb_id'];
    $field_imdb_rating = 0; // This rating should set OMDb queue.
    $field_original_title = $title; // This is non-translatable field.
    $field_overview = $fields['overview'];
    $field_recommended = $item->getApprovedStatus();
    $field_runtime = $fields['runtime'];
    $field_tmdb_id = $item->getId();

    try {
      // Date field.
      $field_release_date = new DateTime($fields['release_date']);

      // ResourceInterface fields.
      $field_collection = NULL;
      if ($fields['belongs_to_collection']) {
        $field_collection = new NodeCollection(
          $fields['belongs_to_collection']['name'],
          $fields['belongs_to_collection']['id']
        );
      }

      $field_poster = NULL;
      if ($fields['poster_path']) {
        $field_poster = (new MediaPoster(
          new FileImageTMDb($fields['poster_path'], 400)
        ))->setAllNameAttributes($title);
      }

      // Collections fields.
      $field_cast = new CollectionParagraphCast();
      // @todo Save all cast?
      foreach (array_slice($fields['credits']['cast'], 0, 5) as $cast_person) {
        $profile_media = NULL;
        if ($cast_person['profile_path']) {
          $profile_media = (new MediaProfile(
            new FileImageTMDb($cast_person['profile_path'], 500)
          ))->setAllNameAttributes($cast_person['name']);
        }
        $field_cast
          ->add(new ParagraphCast(
            new TaxonomyPerson(
              $cast_person['name'],
              $cast_person['id'],
              $profile_media
            ),
            $cast_person['character']
          ));
      }

      $field_crew = new CollectionParagraphCrew();
      // @todo Save all crew?
      foreach (array_slice($fields['credits']['crew'], 0, 5) as $crew_person) {
        $profile_media = NULL;
        if ($crew_person['profile_path']) {
          $profile_media = (new MediaProfile(
            new FileImageTMDb($crew_person['profile_path'], 500)
          ))->setAllNameAttributes($crew_person['name']);
        }

        $field_crew
          ->add(new ParagraphCrew(
            new TaxonomyPerson(
              $crew_person['name'],
              $crew_person['id'],
              $profile_media
            ),
            $crew_person['department'],
            $crew_person['job']
          ));
      }

      $field_genres = new CollectionTaxonomyGenre();
      foreach ($fields['genres'] as $genre) {
        $field_genres
          ->add(new TaxonomyGenre(
            $genre['name'],
            $genre['id']
          ));
      }

      $field_production_companies = new CollectionMediaProductionCompany();
      foreach ($fields['production_companies'] as $production_company) {
        if ($production_company['logo_path']) {
          $field_production_companies
            ->add((new MediaProductionCompany(
              $production_company['id'],
              new FileImageTMDb($production_company['logo_path'], 200)
            ))->setAllNameAttributes($production_company['name']));
        }
      }

      $field_trailers = new CollectionMediaTrailer();
      foreach ($fields["videos"]["results"] as $video) {
        if ($video['site'] == 'YouTube') {
          $field_trailers
            ->add(new MediaTrailer(
              $video['name'],
              $video['size'],
              $video['key']
            ));
        }
        else {
          Drupal::logger(__CLASS__)
            ->notice(t('Trailer use type: %t', ['%t' => $video['site']]));
        }
      }

      $movie = new NodeMovie(
        $title,
        $field_cast,
        $field_collection,
        $field_crew,
        $field_genres,
        $field_imdb_id,
        $field_imdb_rating,
        $field_original_title,
        $field_overview,
        $field_poster,
        $field_production_companies,
        $field_recommended,
        $field_release_date,
        $field_runtime,
        $field_tmdb_id,
        $field_trailers
      );
      $node = $movie->setLanguage($item->getLangObject())->save();

      // Update approved movies after all dependent movies had been saved.
      if ($item->getApprovedStatus() && $item->getLang() === IMDbQueueItemLanguage::ENG) {
        /**
         * @see \Drupal\imdb_saver\Plugin\QueueWorker\RecommendedMovieSaver
         */
        Drupal::queue('recommended_movie_saver')->createItem([
          'nid' => $node->id(),
          'item' => $item,
        ]);
      }
    }
    catch (Exception $e) {
      Drupal::logger(__CLASS__)->error($e->getMessage());
    }
  }

}
