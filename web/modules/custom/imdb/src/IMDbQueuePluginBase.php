<?php

namespace Drupal\imdb;

use Drupal;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Queue\QueueInterface;
use Exception;

/**
 * Class IMDbQueuePluginBase.
 */
abstract class IMDbQueuePluginBase extends PluginBase implements IMDbQueuePluginInterface {

  /**
   * @var QueueInterface
   */
  protected $queue;

  protected $availableCount;

  /**
   * @var \Drupal\Core\State\StateInterface
   */
  protected $drupalState;

  protected $availableCountVariableName;

  /**
   * @var string
   */
  protected $availableCountRefreshTimestampVariableName;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->queue = Drupal::queue($this->pluginId);
    $this->queue->createQueue();

    $this->drupalState = Drupal::service('state');
    $this->availableCountVariableName = "{$this->pluginId}_available_count";
    $this->availableCountRefreshTimestampVariableName = "{$this->pluginId}_refresh_timestamp";

    $this->refreshAvailability();
  }

  /**
   * @param \Drupal\imdb\IMDbQueueItem $itemData
   *
   * @return \Drupal\imdb\IMDbQueueItem
   * @throws \Exception
   */
  abstract protected function prepareItem(IMDbQueueItem $itemData): IMDbQueueItem;

  abstract protected function refreshAvailability(): void;

  public function numberOfItems(): int {
    return $this->queue->numberOfItems();
  }

  public function createItem(IMDbQueueItem $data): void {
    $this->queue->createItem($data);
  }

  public function getResults(): array {
    $start = Drupal::time()->getCurrentTime();

    $results = [];
    while (
      Drupal::time()
        ->getCurrentTime() - $start < IMDbQueuePluginInterface::MAX_EXECUTION_TIME
      &&
      $this->isAvailable()
    ) {
      if ($item = $this->claimItem()) {
        /** @var \Drupal\imdb\IMDbQueueItem $queueItem */
        $queueItem = $item->data;
        try {
          $results[] = $this->prepareItem($queueItem);
        }
        catch (Exception $e) {
          Drupal::logger(get_class($this))->error($e->getMessage());
        } finally {
          // In any case, delete the item.
          $this->deleteItem($item);
          $this->availableCount--;
        }
      }
      else {
        break;
      }
    }
    $this->updateGlobalCountVariable();

    return $results;
  }

  protected function getLimits(): array {
    return $this->getPluginDefinition()['limits'];
  }

  protected function claimItem(): object {
    return $this->queue->claimItem();
  }

  protected function deleteItem($queueItem): void {
    $this->queue->deleteItem($queueItem);
  }

  protected function isAvailable(): bool {
    return (bool) $this->availableCount;
  }

  protected function updateGlobalCountVariable(): void {
    $this->drupalState->set($this->availableCountVariableName, $this->availableCount);
  }

}
