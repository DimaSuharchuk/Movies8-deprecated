<?php

namespace Drupal\imdb_saver\Plugin\IMDbSaver;

use DateTime;
use Drupal;
use Drupal\imdb\IMDbQueueItem;
use Drupal\imdb_saver\IMDbSaverPluginBase;
use Drupal\resource\ParagraphEpisode;

/**
 * Save TMDb result in site entities.
 *
 * @IMDbSaver(
 *   id = "episode_saver"
 * )
 */
class EpisodeSaver extends IMDbSaverPluginBase {

  /**
   * {@inheritDoc}
   */
  public function save(IMDbQueueItem $item): void {
    // Get TV's TMDb ID and season number.
    [$tv_tmdb_id, $season_number] = explode('|', $item->getId());

    // Find season by number in TV.
    $finder = Drupal::service('entity_finder');
    $tv_node_id = $finder
      ->findNodesTv()
      ->byTmdbId($tv_tmdb_id)
      ->reduce()
      ->execute();
    /** @var \Drupal\paragraphs\Entity\Paragraph $season */
    $season = $finder
      ->findParagraphSeason($tv_node_id, $season_number)
      ->loadEntities()
      ->reduce()
      ->execute();
    // Choose needed language.
    $season = $season->getTranslation($item->getLang());

    // Create episodes and save into the field.
    $fields = $item->getFieldsData();
    foreach ($fields['episodes'] as $episode_fields) {
      $episode_resource = new ParagraphEpisode(
        new DateTime($episode_fields['air_date']),
        $episode_fields['episode_number'],
        $this->extractGuestStars($episode_fields),
        $this->extractEpisodeImage($episode_fields['still_path'], $episode_fields['name']),
        $episode_fields['overview'],
        $episode_fields['name'],
        $episode_fields['id']
      );
      $episode_paragraph = $episode_resource
        ->setLanguage($item->getLangObject())
        ->save();
      // Attach episodes to season.
      $season->{'field_episodes'}->appendItem($episode_paragraph);
    }
    // Save season.
    $season->save();
  }

}
