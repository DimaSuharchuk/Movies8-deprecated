<?php

namespace Drupal\imdb;

use Drupal\Component\Plugin\PluginInspectionInterface;

interface IMDbQueuePluginInterface extends PluginInspectionInterface {

  const MAX_EXECUTION_TIME = 5;

  public function numberOfItems(): int;

  public function isEmpty(): bool;

  public function createItem(IMDbQueueItem $data): void;

  public function getResults(): array;

  public function clearQueue(): void;

}
