<?php

namespace Drupal\imdb_saver\Plugin\QueueWorker;

use Drupal;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\Queue\SuspendQueueException;
use Drupal\imdb\EntityFinder;
use Drupal\imdb\IMDbQueueItem;

/**
 * Process a queue of movies and TVs: update recommended and similar fields.
 *
 * @QueueWorker(
 *   id = "recommended_similar_fields_updater",
 *   title = @Translation("Update recommended and similar fields"),
 *   cron = {"time" = 10}
 * )
 */
class RecommendedSimilarFieldsUpdater extends QueueWorkerBase {

  /**
   * We check whether it's possible to set the values of recommended and
   * similar movies or to postpone the saving of this node until later.
   *
   * @inheritDoc
   */
  public function processItem($data) {
    $finder = Drupal::service('entity_finder');

    /** @var \Drupal\node\Entity\Node $node */
    $node = $finder->findNodes()->loadById($data['nid']);

    // This movie or TV already done.
    if ($node->get('field_recommendations')->getValue()
      || $node->get('field_similar')->getValue()
    ) {
      return;
    }

    // If all are good - update fields and save the node.
    foreach ($this->getRecommendationsNodes($data['item'], $finder) as $recommendations_node) {
      $node->{'field_recommendations'}->appendItem($recommendations_node);
    }
    foreach ($this->getSimilarNodes($data['item'], $finder) as $similar_node) {
      $node->{'field_similar'}->appendItem($similar_node);
    }
    $node->set('field_approved', TRUE);
    $node->save();
  }

  /**
   * @param \Drupal\imdb\IMDbQueueItem $item
   * @param \Drupal\imdb\EntityFinder $finder
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   * @throws SuspendQueueException
   */
  private function getRecommendationsNodes(IMDbQueueItem $item, EntityFinder $finder) {
    return $this->getNodes('recommendations', $item, $finder);
  }

  /**
   * @param \Drupal\imdb\IMDbQueueItem $item
   * @param \Drupal\imdb\EntityFinder $finder
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   * @throws SuspendQueueException
   */
  private function getSimilarNodes(IMDbQueueItem $item, EntityFinder $finder) {
    return $this->getNodes('similar', $item, $finder);
  }

  /**
   * Helper method search nodes by TMDb IDs from TMDb response's array
   * "recommended" or "similar", compare exist nodes with TMDb ids and throws
   * exception if nodes not created yet.
   *
   * @param string $type
   *   "recommendations" or "similar", it appears from TMDb response.
   * @param \Drupal\imdb\IMDbQueueItem $item
   * @param \Drupal\imdb\EntityFinder $finder
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   * @throws SuspendQueueException
   */
  private function getNodes(string $type, IMDbQueueItem $item, EntityFinder $finder) {
    $nodes = [];
    // Collect IDs from IMDbQueueItem.
    $tmdb_ids = array_column($item->getFieldsData()[$type]['results'], 'id');
    if ($tmdb_ids) {
      // Search already saved nodes.
      $nodes = $finder->findNodes()
        ->byBundle($item->getRequestType())
        ->byTmdbIds($tmdb_ids)
        ->loadEntities()
        ->execute();
    }
    // This movie or TV can't be saved before all dependencies not saved.
    if (!is_array($nodes) || count($tmdb_ids) !== count($nodes)) {
      // Try to save it later. Suspend queue.
      throw new SuspendQueueException();
    }

    return $nodes;
  }

}
