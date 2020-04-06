<?php

namespace Drupal\imdb_saver\Plugin\QueueWorker;

use Drupal;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\imdb_saver\SaveLater;
use Drupal\node\NodeStorageInterface;

/**
 * Process a queue of movies: update recommended and similar fields.
 *
 * @QueueWorker(
 *   id = "recommended_movie_saver",
 *   title = @Translation("Recommended movie saver"),
 *   cron = {"time" = 30}
 * )
 */
class RecommendedMovieSaver extends QueueWorkerBase {

  /**
   * We check whether it's possible to set the values of recommended and
   * similar movies or to postpone the saving of this node until later.
   *
   * @inheritDoc
   */
  public function processItem($data) {
    /** @var \Drupal\imdb\IMDbQueueItem $item */
    $item = $data['item'];
    $fields = $item->getFieldsData();

    /** @var NodeStorageInterface $storage */
    $storage = Drupal::entityTypeManager()->getStorage('node');
    /** @var \Drupal\node\Entity\Node $movie */
    $movie = $storage->load($data['nid']);
    // This movie already done.
    if ($movie->get('field_recommendations')->getValue()
      || $movie->get('field_similar')->getValue()
    ) {
      return;
    }

    $recommendations_nodes = [];
    // Collect IDs from IMDbQueueItem.
    $recommendations_ids = array_column($fields['recommendations']['results'], 'id');
    if ($recommendations_ids) {
      // Search already saved nodes.
      $recommendations_nodes = $storage->loadByProperties([
        'field_tmdb_id' => $recommendations_ids,
      ]);
    }
    try {
      // This movie can't be saved before all dependencies not saved.
      if (count($recommendations_ids) !== count($recommendations_nodes)) {
        // Try to save it later. Add in the end of queue.
        throw new SaveLater();
      }
      else {
        // To prevent useless search for similar movies if recommended not
        // added, we search for them if all recommended movies already added
        // here.
        // Copy-paste a bit...

        $similar_nodes = [];
        // Collect IDs from IMDbQueueItem.
        $similar_ids = array_column($fields['similar']['results'], 'id');
        if ($similar_ids) {
          // Search already saved nodes.
          $similar_nodes = $storage->loadByProperties([
            'field_tmdb_id' => $similar_ids,
          ]);
        }
        // This movie can't be saved before all dependencies not saved.
        if (count($similar_ids) !== count($similar_nodes)) {
          // Try to save it later. Add in the end of queue.
          throw new SaveLater();
        }
        else {
          // If all are good - update fields and save the node.
          foreach ($recommendations_nodes as $node) {
            $movie->{'field_recommendations'}->appendItem($node);
          }
          foreach ($similar_nodes as $node) {
            $movie->{'field_similar'}->appendItem($node);
          }
          $movie->set('field_recommended', TRUE);
          $movie->save();
        }
      }
    }
    catch (SaveLater $_) {
      // Try to save later in same worker.
      Drupal::queue('recommended_movie_saver')->createItem([
        'nid' => $data['nid'],
        'item' => $data['item'],
      ]);
    }
  }

}
