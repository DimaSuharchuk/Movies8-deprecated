<?php

namespace Drupal\imdb_saver\Plugin\IMDbSaver;

use Drupal;
use Drupal\imdb\IMDbQueueItem;
use Drupal\imdb_saver\IMDbSaverPluginBase;
use Drupal\resource\NodeTv;
use Exception;

/**
 * Save TMDb result in site entities.
 *
 * @IMDbSaver(
 *   id = "tv_saver"
 * )
 */
class TvSaver extends IMDbSaverPluginBase {

  /**
   * {@inheritDoc}
   */
  public function save(IMDbQueueItem $item): void {
    $fields = $item->getFieldsData();

    try {
      $tv = new NodeTv(
        $fields['name'],
        $item->getApprovedStatus(),
        $this->extractCast($item),
        $this->extractCreatedBy($item),
        $this->extractCrew($item),
        $this->extractGenres($item),
        $fields['external_ids']['imdb_id'],
        $fields['in_production'],
        $this->extractNetworks($item),
        $fields['number_of_episodes'],
        $fields['number_of_seasons'],
        $fields['name'],
        $fields['overview'],
        $this->extractPoster($item, $fields['name']),
        $this->extractProductionCompanies($item),
        $this->extractSeasons($item),
        $this->extractEpisodeRuntime($item),
        $fields['homepage'],
        $fields['id'],
        $this->extractTrailers($item)
      );
      $node = $tv->setLanguage($item->getLangObject())->save();

      // Update approved TV after all dependent TV had been saved.
      $this->updateRecommendedAndSimilar($item, $node->id());
    }
    catch (Exception $e) {
      Drupal::logger(__CLASS__)->error($e->getMessage());
    }
  }

}
