<?php

namespace Drupal\alter;

use Drupal\Core\Cron;
use Drupal\Core\CronInterface;
use Drupal\Core\Queue\RequeueException;
use Drupal\Core\Queue\SuspendQueueException;

/**
 * Class CronAlter.
 *
 * Corresponds to Drupal version 8.8.5.
 */
class CronAlter extends Cron implements CronInterface {

  /**
   * {@inheritDoc}
   */
  protected function setCronLastTime() {
    // Record cron time.
    $request_time = $this->time->getRequestTime();
    $this->state->set('system.cron_last', $request_time);
    /**
     * Removed logs from this place in Core.
     */
    // $this->logger->notice('Cron run completed.');
  }

  /**
   * Processes cron queues.
   */
  protected function processQueues() {
    // Grab the defined cron queues.
    foreach ($this->queueManager->getDefinitions() as $queue_name => $info) {
      if (isset($info['cron'])) {
        // Make sure every queue exists. There is no harm in trying to recreate
        // an existing queue.
        $this->queueFactory->get($queue_name)->createQueue();

        $queue_worker = $this->queueManager->createInstance($queue_name);
        $end = time() + (isset($info['cron']['time']) ? $info['cron']['time'] : 15);
        $queue = $this->queueFactory->get($queue_name);
        $lease_time = isset($info['cron']['time']) ?: NULL;
        while (time() < $end && ($item = $queue->claimItem($lease_time))) {
          try {
            $queue_worker->processItem($item->data);
            $queue->deleteItem($item);
          }
          catch (RequeueException $e) {
            // The worker requested the task be immediately requeued.
            $queue->releaseItem($item);
          }
          catch (SuspendQueueException $e) {
            // If the worker indicates there is a problem with the whole queue,
            // release the item and skip to the next queue.
            $queue->releaseItem($item);

            /**
             * Removed exception log.
             */
            // watchdog_exception('cron', $e);

            // Skip to the next queue.
            continue 2;
          }
          catch (\Exception $e) {
            // In case of any other kind of exception, log it and leave the item
            // in the queue to be processed again later.
            watchdog_exception('cron', $e);
          }
        }
      }
    }
  }

}
