<?php

namespace Drupal\remover\Form;

use Drupal;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class RemoverForm extends FormBase {

  public function getFormId() {
    return 'remover_form';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['title'] = [
      '#type' => 'item',
      '#title' => $this->t('What entities should be deleted?'),
    ];

    // Checkbox for selecting everything at once.
    $form['all'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('All'),
    ];

    // Build all checkboxes in form.
    $types = ['node', 'taxonomy_term', 'media', 'paragraph', 'file'];
    foreach ($types as $type) {
      try {
        $this->buildCheckboxes($type, $form);
      }
      catch (PluginNotFoundException $e) {
        Drupal::messenger()
          ->addWarning($this->t('Undefined entity type %type.', [
            '%type' => $type,
          ]));
      }
    }

    // Submit.
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Remove them!'),
      '#attributes' => [
        'class' => ['button', 'button--primary'],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get services.
    $finder = Drupal::service('entity_finder');
    $thread_manager = Drupal::service('remover.thread_manager');

    foreach ($form_state->getValues() as $value => $enabled) {
      // Working with only chosen checkboxes.
      if (is_string($value) && strpos($value, '|') !== FALSE && $enabled) {
        // Get entity type and bundle from checked checkboxes.
        [$type, $bundle] = explode('|', $value);
        // Find entities (ids) of the type and by chosen bundle.
        $ids = $finder->findEntities($type)->byBundle($bundle)->execute();
        // Add ids to remover manager service.
        $thread_manager->process($type, $ids);
      }
    }
  }

  /**
   * Helper method builds a set of checkboxes for some entity type.
   *
   * @param string $type
   *   Entity type, like "node" or "media".
   * @param array $form
   *   The form in which these checkboxes will be inserted.
   *
   * @throws PluginNotFoundException
   */
  private function buildCheckboxes(string $type, array &$form) {
    // Get label of entity type.
    $parent_label = Drupal::entityTypeManager()
      ->getDefinition($type)
      ->getBundleLabel();
    // Get bundles of type.
    $bundles = Drupal::service('entity_type.bundle.info')
      ->getBundleInfo($type);

    // Add parent wrapper.
    $form[$type] = [
      '#type' => 'details',
      '#title' => $parent_label,
      '#open' => TRUE,
    ];
    // Add checkbox "all".
    $form[$type]["{$type}_all"] = [
      '#type' => 'checkbox',
      '#title' => $this->t('All'),
      '#states' => [
        'checked' => [
          ":input[name=all]" => ['checked' => TRUE],
        ],
      ],
    ];
    // Add bundles.
    foreach ($bundles as $bundle_key => $bundle_info) {
      $form[$type]["{$type}|{$bundle_key}"] = [
        '#type' => 'checkbox',
        '#title' => $bundle_info['label'],
        '#states' => [
          'checked' => [
            ":input[name={$type}_all]" => ['checked' => TRUE],
          ],
        ],
      ];
    }
  }

}
