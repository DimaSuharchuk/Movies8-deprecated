<?php

namespace Drupal\imdb_saver;

use Drupal;
use Drupal\Component\Plugin\PluginBase;
use Drupal\imdb\IMDbQueueItem;
use Drupal\imdb\IMDbQueueItemLanguage;
use Drupal\resource\CollectionMediaNetwork;
use Drupal\resource\CollectionMediaProductionCompany;
use Drupal\resource\CollectionMediaTrailer;
use Drupal\resource\CollectionParagraphCast;
use Drupal\resource\CollectionParagraphCrew;
use Drupal\resource\CollectionParagraphSeason;
use Drupal\resource\CollectionTaxonomyGenre;
use Drupal\resource\CollectionTaxonomyPerson;
use Drupal\resource\FileImageTMDb;
use Drupal\resource\MediaEpisodeImage;
use Drupal\resource\MediaNetwork;
use Drupal\resource\MediaPoster;
use Drupal\resource\MediaProductionCompany;
use Drupal\resource\MediaProfile;
use Drupal\resource\MediaTrailer;
use Drupal\resource\ParagraphCast;
use Drupal\resource\ParagraphCrew;
use Drupal\resource\ParagraphSeason;
use Drupal\resource\TaxonomyGenre;
use Drupal\resource\TaxonomyMovieCollection;
use Drupal\resource\TaxonomyPerson;

abstract class IMDbSaverPluginBase extends PluginBase implements IMDbSaverInterface {

  /**
   * Create cast collection from TMDb response.
   *
   * @param \Drupal\imdb\IMDbQueueItem $item
   *
   * @return \Drupal\resource\CollectionParagraphCast
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function extractCast(IMDbQueueItem $item): CollectionParagraphCast {
    $cast_collection = new CollectionParagraphCast();

    // @todo Save all cast?
    foreach (array_slice($item->getFieldsData()['credits']['cast'], 0, 5) as $cast_person) {
      $cast_collection
        ->add(new ParagraphCast(
          $this->extractPerson($cast_person),
          $cast_person['character']
        ));
    }

    return $cast_collection;
  }

  /**
   * Create taxonomy person from TMDb response.
   *
   * @param array $person
   *   Person array from TMDb response.
   *
   * @return \Drupal\resource\TaxonomyPerson
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function extractPerson(array $person): TaxonomyPerson {
    $profile_media = NULL;
    if ($person['profile_path']) {
      $profile_media = (new MediaProfile(
        new FileImageTMDb($person['profile_path'], 500)
      ))->setAllNameAttributes($person['name']);
    }

    return new TaxonomyPerson(
      $person['name'],
      $person['id'],
      $profile_media
    );
  }

  /**
   * Create crew collection from TMDb response.
   *
   * @param \Drupal\imdb\IMDbQueueItem $item
   *
   * @return \Drupal\resource\CollectionParagraphCrew
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function extractCrew(IMDbQueueItem $item): CollectionParagraphCrew {
    $crew_collection = new CollectionParagraphCrew();

    // @todo Save all crew?
    foreach (array_slice($item->getFieldsData()['credits']['crew'], 0, 5) as $crew_person) {
      $crew_collection
        ->add(new ParagraphCrew(
          $this->extractPerson($crew_person),
          $crew_person['department'],
          $crew_person['job']
        ));
    }

    return $crew_collection;
  }

  /**
   * Create genres collection from TMDb response.
   *
   * @param \Drupal\imdb\IMDbQueueItem $item
   *
   * @return \Drupal\resource\CollectionTaxonomyGenre
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function extractGenres(IMDbQueueItem $item): CollectionTaxonomyGenre {
    $genres_collection = new CollectionTaxonomyGenre();

    foreach ($item->getFieldsData()['genres'] as $genre) {
      $genres_collection
        ->add(new TaxonomyGenre(
          $genre['name'],
          $genre['id']
        ));
    }

    return $genres_collection;
  }

  /**
   * Create production companies collection from TMDb response.
   *
   * @param \Drupal\imdb\IMDbQueueItem $item
   *
   * @return \Drupal\resource\CollectionMediaProductionCompany
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function extractProductionCompanies(IMDbQueueItem $item): CollectionMediaProductionCompany {
    $production_companies_collection = new CollectionMediaProductionCompany();

    foreach ($item->getFieldsData()['production_companies'] as $production_company) {
      if ($production_company['logo_path']) {
        $production_companies_collection
          ->add((new MediaProductionCompany(
            $production_company['id'],
            new FileImageTMDb($production_company['logo_path'], 200)
          ))->setAllNameAttributes($production_company['name']));
      }
    }

    return $production_companies_collection;
  }

  /**
   * Create movie collection (it's taxonomy term) collection from TMDb response.
   *
   * @param \Drupal\imdb\IMDbQueueItem $item
   *
   * @return \Drupal\resource\TaxonomyMovieCollection|null
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function extractBelongsToCollection(IMDbQueueItem $item): ?TaxonomyMovieCollection {
    $fields = $item->getFieldsData();

    $movies_collection = NULL;
    if ($fields['belongs_to_collection']) {
      $movies_collection = new TaxonomyMovieCollection(
        $fields['belongs_to_collection']['name'],
        $fields['belongs_to_collection']['id']
      );
    }

    return $movies_collection;
  }

  /**
   * Create trailers collection from TMDb response.
   *
   * @param \Drupal\imdb\IMDbQueueItem $item
   *
   * @return \Drupal\resource\CollectionMediaTrailer
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function extractTrailers(IMDbQueueItem $item): CollectionMediaTrailer {
    $trailers_collection = new CollectionMediaTrailer();

    foreach ($item->getFieldsData()['videos']['results'] as $video) {
      if ($video['site'] == 'YouTube') {
        $trailers_collection
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

    return $trailers_collection;
  }

  /**
   * Create "created by" collection from TMDb response.
   *
   * @param \Drupal\imdb\IMDbQueueItem $item
   *
   * @return \Drupal\resource\CollectionTaxonomyPerson
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function extractCreatedBy(IMDbQueueItem $item): CollectionTaxonomyPerson {
    $created_by_collection = new CollectionTaxonomyPerson();

    foreach ($item->getFieldsData()['created_by'] as $person) {
      $created_by_collection->add($this->extractPerson($person));
    }

    return $created_by_collection;
  }

  /**
   * Create networks collection from TMDb response.
   *
   * @param \Drupal\imdb\IMDbQueueItem $item
   *
   * @return \Drupal\resource\CollectionMediaNetwork
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function extractNetworks(IMDbQueueItem $item): CollectionMediaNetwork {
    $networks_collection = new CollectionMediaNetwork();

    foreach ($item->getFieldsData()['networks'] as $network) {
      if ($network['logo_path']) {
        $networks_collection
          ->add((new MediaNetwork(
            $network['id'],
            new FileImageTMDb($network['logo_path'], 200)
          ))->setAllNameAttributes($network['name']));
      }
    }

    return $networks_collection;
  }

  /**
   * Create seasons collection from TMDb response.
   *
   * @param \Drupal\imdb\IMDbQueueItem $item
   *
   * @return \Drupal\resource\CollectionParagraphSeason
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function extractSeasons(IMDbQueueItem $item): CollectionParagraphSeason {
    $seasons_collection = new CollectionParagraphSeason();

    foreach ($item->getFieldsData()['seasons'] as $season) {
      $seasons_collection->add(new ParagraphSeason(
        $season['episode_count'],
        $season['overview'],
        $this->createPosterFromPath($season['poster_path'], $season['name']),
        $season['season_number'],
        $season['name'],
        $season['id']
      ));
    }

    return $seasons_collection;
  }

  /**
   * Create poster entity from TMDb image name.
   *
   * @param string $remote_image_path
   *   TMDb poster's path from response, only end of image path.
   * @param string $title
   *   This text will be set as alt and title attributes of image.
   *
   * @return \Drupal\resource\MediaPoster|null
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  private function createPosterFromPath(?string $remote_image_path, string $title): ?MediaPoster {
    return $remote_image_path ? (new MediaPoster(
      new FileImageTMDb($remote_image_path, 400)
    ))->setAllNameAttributes($title) : NULL;
  }

  /**
   * Create guest stars collection from TMDb response.
   *
   * @param array $episode_fields
   *
   * @return \Drupal\resource\CollectionParagraphCast
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function extractGuestStars(array $episode_fields): CollectionParagraphCast {
    $stars_collection = new CollectionParagraphCast();

    // @todo Save all cast?
    foreach (array_slice($episode_fields['guest_stars'], 0, 5) as $cast_person) {
      $stars_collection
        ->add(new ParagraphCast(
          $this->extractPerson($cast_person),
          $cast_person['character']
        ));
    }

    return $stars_collection;
  }

  /**
   * Create "Episode image" entity from TMDb response.
   *
   * @param string|null $remote_image_path
   * @param string $title
   *
   * @return \Drupal\resource\MediaEpisodeImage|null
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function extractEpisodeImage(?string $remote_image_path, string $title): ?MediaEpisodeImage {
    return $remote_image_path ? (new MediaEpisodeImage(
      new FileImageTMDb($remote_image_path, 500)
    ))->setAllNameAttributes($title) : NULL;
  }

  /**
   * Create poster entity from TMDb response.
   *
   * @param \Drupal\imdb\IMDbQueueItem $item
   * @param string $title
   *   This text will be set as alt and title attributes of image.
   *
   * @return \Drupal\resource\MediaPoster|null
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function extractPoster(IMDbQueueItem $item, string $title): ?MediaPoster {
    return $this->createPosterFromPath($item->getFieldsData()['poster_path'], $title);
  }

  /**
   * Helper method calculate average runtime.
   *
   * @param \Drupal\imdb\IMDbQueueItem $item
   *
   * @return int
   */
  protected function extractEpisodeRuntime(IMDbQueueItem $item): int {
    $time_array = $item->getFieldsData()['episode_run_time'];

    if (array_filter($time_array)) {
      return round(array_sum($time_array) / count($time_array), 0);
    }

    return 0;
  }

  /**
   * Create queue for update "field_recommended" and "field_similar" for movie
   * or TV.
   *
   * @param \Drupal\imdb\IMDbQueueItem $item
   * @param int $node_id
   */
  protected function updateRecommendedAndSimilar(IMDbQueueItem $item, int $node_id): void {
    if ($item->getApprovedStatus() && $item->getLang() === IMDbQueueItemLanguage::ENG) {
      /**
       * @see \Drupal\imdb_saver\Plugin\QueueWorker\RecommendedSimilarFieldsUpdater
       */
      Drupal::queue('recommended_similar_fields_updater')->createItem([
        'nid' => $node_id,
        'item' => $item,
      ]);
    }
  }

}
