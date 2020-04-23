<?php

namespace Drupal\imdb_saver\Plugin\IMDbSaver;

use Drupal;
use Drupal\imdb\IMDbQueueItem;
use Drupal\imdb_saver\IMDbSaverPluginBase;

/**
 * Save IMDb ID from TMDb result in episode (update paragraph).
 *
 * @IMDbSaver(
 *   id = "episode_imdb_id_saver"
 * )
 */
class EpisodeImdbIdSaver extends IMDbSaverPluginBase {

  /**
   * {@inheritDoc}
   */
  public function save(IMDbQueueItem $item): void {
    // Get TV's TMDb ID, season number and episode number.
    [
      $tv_tmdb_id,
      $season_number,
      $episode_number,
    ] = explode('|', $item->getId());

    // Find episode and set IMDb ID into the field.
    $finder = Drupal::service('entity_finder');
    /** @var \Drupal\node\Entity\Node $tv_node */
    $tv_node_id = $finder
      ->findNodesTv()
      ->byTmdbId($tv_tmdb_id)
      ->reduce()
      ->execute();
    /** @var \Drupal\paragraphs\Entity\Paragraph $season */
    $episode = $finder
      ->findParagraphEpisode($tv_node_id, $season_number, $episode_number)
      ->loadEntities()
      ->reduce()
      ->execute();

    $episode->set('field_imdb_id', $item->getFieldsData()['imdb_id']);
    $episode->save();
  }

}
