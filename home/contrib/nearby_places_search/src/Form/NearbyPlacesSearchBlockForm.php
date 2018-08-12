<?php

namespace Drupal\nearby_places_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Nearby places search block form.
 */
class NearbyPlacesSearchBlockForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'nearby_places_search_block_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    global $base_path;
    $config = $this->config('nearby_places_search.settings');

    $types = [];
    $get_default_latitude = $config->get('nearby_places_search_latitude') ?: 18.5204303;
    $get_default_longitude = $config->get('nearby_places_search_longitude') ?: 73.8567436;
    $get_default_loc_text = $config->get('nearby_places_search_location_title') ?: 'Pune, Maharashtra, India';
    $get_default_radius = $config->get('nearby_places_search_radius') ?: 1000;
    $get_default_types = $config->get('nearby_places_search_types') ?: [];

    $marker_library_path = '';
    if (nearby_places_search_library_check()) {
      $status_report = $base_path . 'admin/reports/status';
      $help_link = $base_path . 'admin/help/nearby_places_search';
      $missing_marker_library = $this->t('Nearby places search marker library could not be found. Please check <a target="_blank" href="@status_report">Status report</a> or <a target="_blank" href="@help_link">Help</a> for configuration settings.', ['@status_report' => $status_report, '@help_link' => $help_link]);
      drupal_set_message($missing_marker_library, 'warning');
    }
    else {
      $marker_library_path = $base_path . libraries_get_path('nearby_places_search.markers');
    }

    if (nearby_places_search_build_api_msg()) {
      drupal_set_message(nearby_places_search_build_api_msg(), 'warning');
    }
    foreach ($get_default_types as $key => $value) {
      if ($value) {
        $types[$key] = ucwords(str_replace('_', ' ', $key));
      }
      else {
        unset($get_default_types[$key]);
      }
    }
    if (empty($get_default_types)) {
      $types = [
        'atm' => $this->t('Atm'),
        'bank' => $this->t('Bank'),
        'hospital' => $this->t('Hospital'),
        'park' => $this->t('Park'),
        'restaurant' => $this->t('Restaurant'),
        'school' => $this->t('School'),
      ];
    }

    $form['#attached'] = [
      'library' => [
        'nearby_places_search/nearby_places_search_admin',
        'nearby_places_search/nearby_places_search_block',
        'nearby_places_search/google-map-apis',
      ],
      'drupalSettings' => [
        'nearby_places_variable' => [
          'img_path' => $marker_library_path,
          'default_latitude' => $get_default_latitude,
          'default_longitude' => $get_default_longitude,
          'default_radius' => $get_default_radius,
        ],
      ],
    ];

    $form['types'] = [
      '#type' => 'radios',
      '#title' => $this->t('Location Types'),
      '#options' => $types,
      '#attributes' => ['class' => ['radio_btn']],
    ];

    $form['address'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Address'),
      '#default_value' => $get_default_loc_text,
    ];

    $form['custom_btn'] = [
      '#markup' => $this->t('Search'),
    ];

    $form['latitude'] = [
      '#type' => 'hidden',
      '#attributes' => ['id' => 'latitude', 'placeholder' => $this->t('Latitude')],
      '#default_value' => $get_default_latitude,
    ];

    $form['longitude'] = [
      '#type' => 'hidden',
      '#attributes' => ['id' => 'longitude', 'placeholder' => $this->t('Longitude')],
      '#default_value' => $get_default_longitude,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

}
