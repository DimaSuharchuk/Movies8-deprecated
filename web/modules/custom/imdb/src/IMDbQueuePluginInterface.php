<?php

namespace Drupal\imdb;

use Drupal\Component\Plugin\PluginInspectionInterface;

interface IMDbQueuePluginInterface extends PluginInspectionInterface {

  const MAX_EXECUTION_TIME = 5;

//  public function getLimits(): array;
//
  public function numberOfItems(): int;
//
  public function createItem(IMDbQueueItem $data);
//
//  public function claimItem(): object;
//
//  public function deleteItem($queueItem): void;
//
//  public function isAvailable(): bool;

  public function getResults(): array;

}
