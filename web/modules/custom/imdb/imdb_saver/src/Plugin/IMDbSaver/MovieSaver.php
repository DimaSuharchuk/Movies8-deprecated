<?php

namespace Drupal\imdb_saver\Plugin\IMDbSaver;

use DateTime;
use Drupal;
use Drupal\imdb\IMDbQueueItem;
use Drupal\imdb_saver\IMDbSaverPluginBase;
use Drupal\resource\NodeMovie;
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

    try {
      $movie = new NodeMovie(
        $fields['title'],
        $item->getApprovedStatus(),
        $this->extractCast($item),
        $this->extractBelongsToCollection($item),
        $this->extractCrew($item),
        $this->extractGenres($item),
        $fields['imdb_id'],
        $fields['title'],
        $fields['overview'],
        $this->extractPoster($item, $fields['title']),
        $this->extractProductionCompanies($item),
        new DateTime($fields['release_date']),
        $fields['runtime'],
        $fields['homepage'],
        $item->getId(),
        $this->extractTrailers($item)
      );
      $node = $movie->setLanguage($item->getLangObject())->save();

      // Update approved movies after all dependent movies had been saved.
      $this->updateRecommendedAndSimilar($item, $node->id());
    }
    catch (Exception $e) {
      Drupal::logger(__CLASS__)->error($e->getMessage());
    }
  }

}
