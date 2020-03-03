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
    /** @var \Drupal\Core\File\FileSystem $fileSystem */
    $fileSystem = Drupal::service('file_system');
    /** @var \Drupal\Core\Site\Settings $privateSystem */
    $privateSystem = Settings::get('file_private_path');
    /** @var \Drupal\Core\Datetime\DateFormatterInterface $dateFormatter */
    $dateFormatter = Drupal::service('date.formatter');
    /** @var \Drupal\Core\State\StateInterface $state */
    $state = Drupal::service('state');
    /** @var \Drupal\imdb\IMDbQueueManager $imdbQueueManager */
    $imdbQueueManager = Drupal::service('plugin.manager.imdb_queue');
    /** @var \Drupal\imdb\Plugin\IMDbQueue\TMDbQueue $tmdbQueue */
    /** @var \Drupal\imdb\Plugin\IMDbQueue\OMDbQueue $omdbQueue */
    try {
      $tmdbQueue = $imdbQueueManager->createInstance('tmdb_queue');
      $omdbQueue = $imdbQueueManager->createInstance('omdb_queue');
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
      '#default_value' => $privateSystem && $fileSystem->prepareDirectory($privateSystem, NULL),
      '#disabled' => TRUE,
    ];
    // Cron info.
    $form['important']['cron'] = [
      '#type' => 'item',
      '#markup' => $this->t('Cron last run: %time ago.', [
        '%time' => $dateFormatter->formatTimeDiffSince($state->get('system.cron_last')),
      ]),
    ];

    // Statistics.
    $form['statistics'] = [
      '#type' => 'details',
      '#title' => $this->t('Statistics'),
      '#open' => TRUE,
    ];
    // Nodes count.
    $form['statistics']['nodes_count'] = [
      '#type' => 'item',
      '#markup' => $this->t('Nodes count: %count.', [
        '%count' => '???',
      ]),
    ];
    // Approved nodes count.
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
        '%count' => '???',
      ]),
    ];
    // TV count.
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
        '%count' => $tmdbQueue->numberOfItems(),
      ]),
    ];
    // OMDb Queue.
    $form['statistics']['omdb_queue'] = [
      '#type' => 'item',
      '#markup' => $this->t('Untreated OMDb queue items: %count.', [
        '%count' => $omdbQueue->numberOfItems(),
      ]),
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get IMDb IDs from form.
    $input_ids = $form_state->getValue('imdb_ids');
    $input_ids = explode("\r\n", $input_ids);

    /** @var \Drupal\imdb\IMDbHelper $imdbHelper */
    $imdbHelper = Drupal::service('imdb.helper');

    // Collect valid IDs.
    $ids = [];
    foreach ($input_ids as $input_id) {
      if ($imdbHelper->isImdbId($input_id)) {
        $ids[] = $input_id;
      }
    }

    if ($ids) {
      // @todo Замість збереження в файл краще шукати по нодах і тоді вирішувати додавати в чергу чи ні.

      $newIds = $ids; // @todo

      // Add new IMDb IDs to TMDb Queue.
      if ($newIds) {
        /** @var \Drupal\imdb\IMDbQueueManager $imdbQueueManager */
        $imdbQueueManager = Drupal::service('plugin.manager.imdb_queue');
        /** @var \Drupal\imdb\Plugin\IMDbQueue\TMDbQueue $tmdbQueue */
        try {
          $tmdbQueue = $imdbQueueManager->createInstance('tmdb_queue');

          $type = IMDbQueueItemRequestType::FIND();
          $lang = IMDbQueueItemLanguage::ENG();

          foreach ($newIds as $id) {
            $item = new IMDbQueueItem($type, $id, $lang);
            $tmdbQueue->createItem($item);
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

}
