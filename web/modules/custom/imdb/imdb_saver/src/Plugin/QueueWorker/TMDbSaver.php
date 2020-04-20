<?php

namespace Drupal\imdb_saver\Plugin\QueueWorker;

use Drupal;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\imdb\IMDbQueueItem;
use Drupal\imdb\IMDbQueueItemRequestType;

/**
 * Process a queue of TMDb result items to save on site.
 *
 * @QueueWorker(
 *   id = "tmdb_result_saver",
 *   title = @Translation("TMDb result saver"),
 *   cron = {"time" = 30}
 * )
 */
class TMDbSaver extends QueueWorkerBase {

  use StringTranslationTrait;

  /**
   * @inheritDoc
   */
  public function processItem($data) {
    $plugin_id = NULL;

    /** @var IMDbQueueItem $item */
    $item = $data['item'];
    switch ($item->getRequestType()) {
      case IMDbQueueItemRequestType::FIND:
        // Nothing to save for this type.
        break;

      case IMDbQueueItemRequestType::MOVIE:
        /**
         * @see \Drupal\imdb_saver\Plugin\IMDbSaver\MovieSaver
         */
        $plugin_id = 'movie_saver';
        break;

      case IMDbQueueItemRequestType::TV:
        /**
         * @see \Drupal\imdb_saver\Plugin\IMDbSaver\TvSaver
         */
        $plugin_id = 'tv_saver';
        break;

      case IMDbQueueItemRequestType::SEASON:
        /**
         * @see \Drupal\imdb_saver\Plugin\IMDbSaver\EpisodeSaver
         */
        $plugin_id = 'episode_saver';
        // This creates confusion in the names. In fact, the episodes will be
        // saved here, because the TMDb API get information about all
        // episodes of season, just like at the time of saving a TV show we
        // have data about the season.
        break;

      case IMDbQueueItemRequestType::EPISODE_EXTERNAL_IDS:
        /**
         * @see \Drupal\imdb_saver\Plugin\IMDbSaver\EpisodeImdbIdSaver
         */
        $plugin_id = 'episode_imdb_id_saver';
        break;

      default:
        Drupal::logger(__CLASS__)
          ->error($this->t('Undefined request type %type.', [
            '%type' => $item->getRequestType(),
          ]));
        return;
    }

    if ($plugin_id) {
      /** @var \Drupal\imdb_saver\IMDbSaverManager $imdb_saver_manager */
      $imdb_saver_manager = Drupal::service('plugin.manager.imdb_saver');
      /** @var \Drupal\imdb_saver\IMDbSaverInterface $saver */
      $saver = $imdb_saver_manager->createInstance($plugin_id);
      $saver->save($item);
    }
  }

}
