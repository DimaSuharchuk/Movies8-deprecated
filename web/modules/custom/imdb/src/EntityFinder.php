<?php

namespace Drupal\imdb;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;

class EntityFinder {

  /**
   * @var EntityTypeManagerInterface
   */
  private $entity_type_manager;

  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  private $storage;

  private $search_values = [];

  /**
   * @var int
   */
  private $limit;

  /**
   * @var bool
   */
  private $reduce = FALSE;

  /**
   * @var bool
   */
  private $count = FALSE;

  /**
   * @var bool
   */
  private $load = FALSE;


  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entity_type_manager = $entity_type_manager;
  }

  public function findNodesMovie(): self {
    return $this->findNodes()->byBundle(IMDbQueueItemRequestType::MOVIE);
  }

  public function findNodesTv(): self {
    return $this->findNodes()->byBundle(IMDbQueueItemRequestType::TV);
  }

  public function findParagraphSeason(int $parent_tv_nid, int $season_number): self {
    $this->findParagraphs()->byBundle(IMDbQueueItemRequestType::SEASON);
    $this->addCondition('parent_id', $parent_tv_nid);
    $this->addCondition('field_season_number', $season_number);
    return $this;
  }

  public function findParagraphEpisode(int $parent_tv_nid, int $season_number, int $episode_number): self {
    $season_id = $this->findParagraphSeason($parent_tv_nid, $season_number)
      ->reduce()
      ->execute();

    $this->findParagraphs()->byBundle(IMDbQueueItemRequestType::EPISODE);
    $this->addCondition('parent_id', $season_id);
    $this->addCondition('field_episode_number', $episode_number);
    return $this;
  }

  public function findNodes(): self {
    return $this->findEntities('node');
  }

  public function findParagraphs(): self {
    return $this->findEntities('paragraph');
  }

  public function findEntities(string $type): self {
    $this->getStorage($type);
    return $this;
  }

  public function byBundle(string $bundle): self {
    return $this->byBundles([$bundle]);
  }

  public function byBundles(array $bundles): self {
    try {
      $bundle_key = $this->entity_type_manager
        ->getDefinition($this->storage->getEntityTypeId())
        ->getKey('bundle');
      if ($bundle_key) {
        $this->search_values[$bundle_key] = $bundles;
      }
    }
    catch (PluginNotFoundException $e) {
    }
    return $this;
  }

  public function byTmdbId(int $tmdb_id): self {
    return $this->byTmdbIds([$tmdb_id]);
  }

  public function byTmdbIds(array $tmdb_ids): self {
    return $this->addCondition('field_tmdb_id', $tmdb_ids);
  }

  public function addCondition(string $property, $value): self {
    $this->search_values[$property] = $value;
    return $this;
  }

  /**
   * @return $this
   */
  public function reduce(): self {
    $this->limit = 1;
    $this->reduce = TRUE;
    return $this;
  }

  public function limit(int $limit): self {
    $this->limit = $limit > 0 ? $limit : 0;
    return $this;
  }

  public function count(): self {
    $this->count = TRUE;
    return $this;
  }

  public function loadEntities(): self {
    $this->load = TRUE;
    return $this;
  }

  public function execute() {
    $return = $ids = $this->findByProperties($this->search_values);

    if ($this->count) {
      $return = count($ids);
    }

    if ($this->load) {
      $return = $ids ? $this->loadMultipleById($ids) : [];
    }

    $this->storage = NULL;
    $this->search_values = [];
    $this->limit = 0;
    $this->count = FALSE;
    $this->load = FALSE;

    return $this->reduce && is_array($return) ? reset($return) : $return;
  }


  public function loadById(int $id): ?EntityInterface {
    $entities = $this->loadMultipleById([$id]);
    return $entities ? reset($entities) : NULL;
  }

  /**
   * @param array $ids
   *
   * @return EntityInterface[]
   */
  public function loadMultipleById(array $ids): array {
    return $this->storage ? $this->storage->loadMultiple($ids) : [];
  }


  /**
   * Builds an entity query.
   *
   * @param \Drupal\Core\Entity\Query\QueryInterface $entity_query
   *   EntityQuery instance.
   * @param array $values
   *   An associative array of properties of the entity, where the keys are the
   *   property names and the values are the values those properties must have.
   */
  private function buildPropertyQuery(QueryInterface $entity_query, array $values) {
    foreach ($values as $name => $value) {
      // Cast scalars to array so we can consistently use an IN condition.
      $entity_query->condition($name, (array) $value, 'IN');
    }
  }

  /**
   * @param array $values
   *
   * @return array|\Drupal\Core\Entity\EntityInterface[]
   */
  private function findByProperties(array $values = []): array {
    if ($this->storage) {
      // Build a query to fetch the entity IDs.
      $entity_query = $this->storage->getQuery();
      $entity_query->accessCheck(FALSE);
      if ($this->limit) {
        $entity_query->range(0, $this->limit);
      }
      $this->buildPropertyQuery($entity_query, $values);
      $result = $entity_query->execute();

      return $result ?: [];
    }
    return [];
  }

  private function getStorage(string $type): void {
    try {
      $this->storage = $this->entity_type_manager->getStorage($type);
    }
    catch (PluginException $_) {
      $this->storage = NULL;
    }
  }

}
