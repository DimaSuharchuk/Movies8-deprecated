<?php

namespace Drupal\alter;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Class AlterServiceProvider.
 *
 * @package Drupal\alter
 */
class AlterServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritDoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('cron');
    $definition->setClass('Drupal\alter\CronAlter');
  }

}
