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

  private $TMDbClient;

  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $apiKey = Settings::get('tmdb_api_key');
    $token = new ApiToken($apiKey);
    $this->TMDbClient = new Client($token);
  }

  /**
   * @param \Drupal\imdb\IMDbQueueItem $itemData
   *
   * @return \Drupal\imdb\IMDbQueueItem
   * @throws \Exception
   */
  protected function prepareItem(IMDbQueueItem $itemData): IMDbQueueItem {
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
        $res = $itemData->getRequestType() === IMDbQueueItemRequestType::MOVIE ? $this->TMDbClient->getMoviesApi()
          ->getMovie($itemData->getId(), $options) : $this->TMDbClient->getTvApi()
          ->getTvshow($itemData->getId(), $options);

        // Set response to Queue object.
        $itemData->setFieldsData($res);

        // Create queue items for recommendations and similar movies and tv, for approved movies and tv only.
        if ($itemData->getApprovedStatus()) {
          foreach (['recommendations', 'similar'] as $a) {
            foreach ($res[$a]['results'] as $result) {
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
    }

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
