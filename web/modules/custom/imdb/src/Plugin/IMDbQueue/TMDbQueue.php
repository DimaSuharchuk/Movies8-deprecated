<?php

namespace Drupal\imdb\Plugin\IMDbQueue;

use Drupal\Core\Site\Settings;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\imdb\IMDbQueueItem;
use Drupal\imdb\IMDbQueueItemLanguage;
use Drupal\imdb\IMDbQueueItemRequestType;
use Drupal\imdb\IMDbQueuePluginBase;
use Exception;
use Tmdb\ApiToken;
use Tmdb\Client;

/**
 * Class TMDbQueue.
 *
 * @IMDbQueue(
 *   id = "tmdb_queue",
 *   limits = {
 *     "minute" = "40"
 *   }
 * )
 */
class TMDbQueue extends IMDbQueuePluginBase {

  use StringTranslationTrait;

  /**
   * @var \Tmdb\Client
   */
  private $TMDbClient;

  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $apiKey = Settings::get('tmdb_api_key');
    $token = new ApiToken($apiKey);
    $this->TMDbClient = new Client($token);
  }

  /**
   * {@inheritDoc}
   *
   * @param \Drupal\imdb\IMDbQueueItem $itemData
   *
   * @return \Drupal\imdb\IMDbQueueItem
   * @throws \Exception
   */
  protected function prepareItem(IMDbQueueItem $itemData): IMDbQueueItem {
    $fields_data = [];

    switch ($itemData->getRequestType()) {
      case IMDbQueueItemRequestType::FIND:
        // Request to API.
        $res = $this->TMDbClient->getFindApi()
          ->findBy($itemData->getId(), ['external_source' => 'imdb_id']);

        foreach ($res as $k => $v) {
          if ($v) {
            switch ($k) {
              case 'movie_results':
                $type = IMDbQueueItemRequestType::MOVIE();
                break;
              case 'tv_results':
                $type = IMDbQueueItemRequestType::TV();
                break;
              default:
                throw new Exception($this->t('Undefined type %t returned from API\'s method FIND.', ['%t' => $k]));
            }
            // All Movie or TV item for each language into queue.
            foreach (IMDbQueueItemLanguage::members() as $lang) {
              $queueItem = new IMDbQueueItem($type, $v[0]['id'], $lang);
              $queueItem->setApprovedStatus(TRUE);
              $this->createItem($queueItem);
            }
            break; // No need to continue check other empty sub-arrays.
          }
        }
        break;

      case IMDbQueueItemRequestType::MOVIE:
      case IMDbQueueItemRequestType::TV:
        // Request to API.
        $options = [
          'language' => $itemData->getLang(),
          'append_to_response' => 'recommendations,similar,videos,credits,external_ids',
        ];
        $fields_data = $itemData->getRequestType() === IMDbQueueItemRequestType::MOVIE ? $this->TMDbClient->getMoviesApi()
          ->getMovie($itemData->getId(), $options) : $this->TMDbClient->getTvApi()
          ->getTvshow($itemData->getId(), $options);

        if ($itemData->getRequestType() === IMDbQueueItemRequestType::TV) {
          foreach ($fields_data['seasons'] as $season) {
            $queueItem = new IMDbQueueItem(
              IMDbQueueItemRequestType::SEASON(),
              $fields_data['id'] . '|' . $season['season_number'],
              $itemData->getLangObject()
            );
            $this->createItem($queueItem);
          }
        }

        // Create queue items for recommendations and similar movies and tv, for approved movies and tv only.
        if ($itemData->getApprovedStatus()) {
          foreach (['recommendations', 'similar'] as $a) {
            foreach ($fields_data[$a]['results'] as $result) {
              $queueItem = new IMDbQueueItem(
                $itemData->getRequestTypeObject(),
                $result['id'],
                $itemData->getLangObject()
              );
              $queueItem->setApprovedStatus(FALSE);
              $this->createItem($queueItem);
            }
          }
        }
        break;

      case IMDbQueueItemRequestType::SEASON:
        // Get TV's TMDb ID and season number.
        [$tv_id, $season_number] = explode('|', $itemData->getId());

        $options = [
          'language' => $itemData->getLang(),
        ];
        $fields_data = $this->TMDbClient->getTvSeasonApi()
          ->getSeason($tv_id, $season_number, $options);

        // We need to saving IMDb IDs of episodes, add episodes for request to
        // TMDb API.
        if ($itemData->getLang() === IMDbQueueItemLanguage::ENG) {
          foreach ($fields_data['episodes'] as $episode) {
            // As a rule episodes in season with "zero number", aka "Specials"
            // haven't IMDb ID.
            if ($season_number) {
              $queueItem = new IMDbQueueItem(
                IMDbQueueItemRequestType::EPISODE_EXTERNAL_IDS(),
                $itemData->getId() . '|' . $episode['episode_number'],
                IMDbQueueItemLanguage::ENG()
              );
              $this->createItem($queueItem);
            }
          }
        }
        break;

      case IMDbQueueItemRequestType::EPISODE_EXTERNAL_IDS:
        // Get TV's TMDb ID, season number and episode number.
        [
          $tv_id,
          $season_number,
          $episode_number,
        ] = explode('|', $itemData->getId());
        $fields_data = $this->TMDbClient->getTvEpisodeApi()
          ->getExternalIds($tv_id, $season_number, $episode_number);
        break;
    }

    // Set response to Queue object.
    $itemData->setFieldsData($fields_data);

    return $itemData;
  }

  protected function refreshAvailability(): void {
    // There is no need to write any complicated logic, because cron can't run
    // more than 1 time per minute.
    // Every fresh queue can run with full limits (number of attempts).
    // Just set max number of attempts to get queue items.
    $this->availableCount = $this->getLimits()['minute'];
  }

  protected function updateGlobalCountVariable(): void {
    // Not necessary to use global variables for this queue.
  }

}
