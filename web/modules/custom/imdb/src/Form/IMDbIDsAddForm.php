<?php

namespace Drupal\imdb\Form;

use Drupal;
use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Site\Settings;
use Drupal\imdb\IMDbQueueItem;
use Drupal\imdb\IMDbQueueItemLanguage;
use Drupal\imdb\IMDbQueueItemRequestType;

/**
 * Class IMDbIDsAddForm.
 *
 * @package Drupal\imdb\Form
 */
class IMDbIDsAddForm extends FormBase {

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'imdb_ids_add_form';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Used services.
    /** @var \Drupal\Core\File\FileSystem $file_system */
    $file_system = Drupal::service('file_system');
    /** @var \Drupal\Core\Site\Settings $private_system */
    $private_system = Settings::get('file_private_path');
    /** @var \Drupal\Core\Datetime\DateFormatterInterface $date_formatter */
    $date_formatter = Drupal::service('date.formatter');
    /** @var \Drupal\Core\State\StateInterface $state */
    $state = Drupal::service('state');
    /** @var \Drupal\imdb\IMDbQueueManager $imdb_queue_manager */
    $imdb_queue_manager = Drupal::service('plugin.manager.imdb_queue');
    /** @var \Drupal\imdb\IMDbQueuePluginInterface $tmdb_queue */
    /** @var \Drupal\imdb\IMDbQueuePluginInterface $omdb_queue */
    try {
      $tmdb_queue = $imdb_queue_manager->createInstance('tmdb_queue');
      $omdb_queue = $imdb_queue_manager->createInstance('omdb_queue');
    }
    catch (PluginException $e) {
      Drupal::messenger()->addError($e->getMessage());
    }

    // Check API keys.
    $disabled = FALSE;
    if (!Settings::get('tmdb_api_key')) {
      Drupal::messenger()->addError($this->t('TMDb API key is not defined.'));
      $disabled = TRUE;
    }
    if (!Settings::get('omdb_api_key')) {
      Drupal::messenger()->addError($this->t('OMDb API key is not defined.'));
      $disabled = TRUE;
    }

    $form['imdb_ids'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Add IMDb IDs'),
      '#required' => TRUE,
      '#disabled' => $disabled,
    ];

    $form['actions'] = [
      '#type' => 'actions',
      '#weight' => 0,
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add'),
      '#disabled' => $disabled,
      '#submit' => ['::submitForm'],
    ];
    /**
     * @see IMDbIDsAddForm::clearTMDbQueue()
     */
    $form['actions']['clear'] = [
      '#type' => 'submit',
      '#value' => $this->t('Clear TMDb queue'),
      '#submit' => ['::clearTMDbQueue'],
      '#limit_validation_errors' => [],
    ];

    $form['important'] = [
      '#type' => 'details',
      '#title' => $this->t('Other important settings'),
      '#open' => TRUE,
    ];
    // Private file system.
    $form['important']['private_system'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Private file system exists and directory is readable.'),
      '#default_value' => $private_system && $file_system->prepareDirectory($private_system, NULL),
      '#disabled' => TRUE,
    ];
    // Cron info.
    $form['important']['cron'] = [
      '#type' => 'item',
      '#markup' => $this->t('Cron last run: %time ago.', [
        '%time' => $date_formatter->formatTimeDiffSince($state->get('system.cron_last')),
      ]),
    ];


    /**
     * Statistics.
     */
    /** @var \Drupal\node\NodeStorageInterface $node_storage */
    try {
      $node_storage = Drupal::entityTypeManager()->getStorage('node');
    }
    catch (PluginException $e) {
      Drupal::messenger()->addError($e->getMessage());
    }
    $movies = $node_storage->loadByProperties([
      'type' => 'movie',
    ]);
    $movies_count = count($movies);
    // Statistics fieldset.
    $form['statistics'] = [
      '#type' => 'details',
      '#title' => $this->t('Statistics'),
      '#open' => TRUE,
    ];
    // Nodes count.
    // @todo
    $form['statistics']['nodes_count'] = [
      '#type' => 'item',
      '#markup' => $this->t('Nodes count: %count.', [
        '%count' => '???',
      ]),
    ];
    // Approved nodes count.
    // @todo
    $form['statistics']['approved_nodes_count'] = [
      '#type' => 'item',
      '#markup' => $this->t('Approved nodes count: %count.', [
        '%count' => '???',
      ]),
    ];
    // Movies count.
    $form['statistics']['movies_count'] = [
      '#type' => 'item',
      '#markup' => $this->t('Movies count: %count.', [
        '%count' => $movies_count,
      ]),
    ];
    // TV count.
    // @todo
    $form['statistics']['tv_count'] = [
      '#type' => 'item',
      '#markup' => $this->t('TV count: %count.', [
        '%count' => '???',
      ]),
    ];

    // TMDb Queue.
    $form['statistics']['tmdb_queue'] = [
      '#type' => 'item',
      '#markup' => $this->t('Untreated TMDb queue items: %count.', [
        '%count' => $tmdb_queue->numberOfItems(),
      ]),
    ];
    // OMDb Queue.
    $form['statistics']['omdb_queue'] = [
      '#type' => 'item',
      '#markup' => $this->t('Untreated OMDb queue items: %count.', [
        '%count' => $omdb_queue->numberOfItems(),
      ]),
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get IMDb IDs from form.
    $input_ids = $form_state->getValue('imdb_ids');
    $input_ids = explode("\r\n", $input_ids);

    /** @var \Drupal\imdb\IMDbHelper $imdb_helper */
    $imdb_helper = Drupal::service('imdb.helper');

    // Collect valid IDs.
    $ids = [];
    foreach ($input_ids as $input_id) {
      if ($imdb_helper->isImdbId($input_id)) {
        $ids[] = $input_id;
      }
    }

    if ($ids) {
      /** @var \Drupal\node\NodeStorageInterface $node_storage */
      $node_storage = Drupal::entityTypeManager()->getStorage('node');

      // Search nodes with imdb ids, also node should be approved. Non-approved
      // nodes could be approved later.
      $nodes = $node_storage->loadByProperties([
        'field_imdb_id' => $ids,
        'field_approved' => TRUE,
      ]);

      // Filter new imdb ids from already added nodes with some imdb ids.
      // @todo Check this logic, maybe it's better to add all IDs and check it
      //   later, before fetching. Because some resources (nodes) may not have all
      //   translations for various reasons (failures).
      if ($nodes) {
        $imdb_ids = [];
        /** @var \Drupal\node\Entity\Node $node */
        foreach ($nodes as $nid => $node) {
          $imdb_ids[] = $node->get('field_imdb_id')->value;
        }
        $new_ids = array_diff($ids, $imdb_ids);
      }
      else {
        $new_ids = $ids;
      }

      // Add new IMDb IDs to TMDb Queue.
      if ($new_ids) {
        /** @var \Drupal\imdb\IMDbQueueManager $imdb_queue_manager */
        $imdb_queue_manager = Drupal::service('plugin.manager.imdb_queue');
        /** @var \Drupal\imdb\Plugin\IMDbQueue\TMDbQueue $tmdb_queue */
        try {
          $tmdb_queue = $imdb_queue_manager->createInstance('tmdb_queue');

          $type = IMDbQueueItemRequestType::FIND();
          $lang = IMDbQueueItemLanguage::ENG();

          foreach ($new_ids as $id) {
            $item = new IMDbQueueItem($type, $id, $lang);
            $tmdb_queue->createItem($item);
          }
        }
        catch (PluginException $e) {
          Drupal::messenger()->addError($e->getMessage());
          Drupal::messenger()
            ->addError($this->t('IMDb IDs haven\'t added to queue.'));
        }
      }
    }

  }

  /**
   * Clear TMDb Queue manually.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function clearTMDbQueue() {
    /** @var \Drupal\imdb\IMDbQueueManager $imdb_queue_manager */
    $imdb_queue_manager = Drupal::service('plugin.manager.imdb_queue');
    /** @var \Drupal\imdb\IMDbQueuePluginInterface $tmdb_queue */
    $tmdb_queue = $imdb_queue_manager->createInstance('tmdb_queue');
    $tmdb_queue->clearQueue();
  }

}
