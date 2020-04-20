<?php

namespace Drupal\imdb_saver\Plugin\QueueWorker;

use Drupal;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\imdb_saver\SaveLater;

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
    $node = $finder->findNodes()->load($data['nid']);

    // This movie or TV already done.
    if ($node->get('field_recommendations')->getValue()
      || $node->get('field_similar')->getValue()
    ) {
      return;
    }

    /** @var \Drupal\imdb\IMDbQueueItem $item */
    $item = $data['item'];
    $fields = $item->getFieldsData();

    $recommendations_nodes = [];
    // Collect IDs from IMDbQueueItem.
    $recommendations_ids = array_column($fields['recommendations']['results'], 'id');
    if ($recommendations_ids) {
      // Search already saved nodes.
      $recommendations_nodes = $finder->findNodes()
        ->byBundle($item->getRequestType())
        ->byTmdbIds($recommendations_ids)
        ->execute();
    }
    try {
      // This movie or TV can't be saved before all dependencies not saved.
      if (!is_array($recommendations_nodes) || count($recommendations_ids) !== count($recommendations_nodes)) {
        // Try to save it later. Add in the end of queue.
        throw new SaveLater();
      }
      else {
        // To prevent useless search for similar nodes if recommended not
        // added, we search for them if all recommended movies already added
        // here.
        // Copy-paste a bit...

        $similar_nodes = [];
        // Collect IDs from IMDbQueueItem.
        $similar_ids = array_column($fields['similar']['results'], 'id');
        if ($similar_ids) {
          // Search already saved nodes.
          $similar_nodes = $finder->findNodes()
            ->byBundle($item->getRequestType())
            ->byTmdbIds($similar_ids)
            ->execute();
        }
        // This movie or TV can't be saved before all dependencies not saved.
        if (!is_array($similar_nodes) || count($similar_ids) !== count($similar_nodes)) {
          // Try to save it later. Add in the end of queue.
          throw new SaveLater();
        }
        else {
          // If all are good - update fields and save the node.
          foreach ($recommendations_nodes as $recommendations_node) {
            $node->{'field_recommendations'}->appendItem($recommendations_node);
          }
          foreach ($similar_nodes as $similar_node) {
            $node->{'field_similar'}->appendItem($similar_node);
          }
          $node->set('field_approved', TRUE);
          $node->save();
        }
      }
    }
    catch (SaveLater $_) {
      // Try to save later in same worker.
      Drupal::queue('recommended_similar_fields_updater')->createItem([
        'nid' => $data['nid'],
        'item' => $item,
      ]);
    }
  }

}
