<?php

namespace Drupal\nearby_places_search\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SettingsForm.
 *
 * @package Drupal\nearby_places_search\Form
 */
class NearbyPlacesSearchForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'nearby_places_search.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'Nearby_places_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('nearby_places_search.settings');
    $get_default = $config->get('nearby_places_search_types') ?: [];
    $types = [
      'accounting' => $this->t('Accounting'),
      'airport' => $this->t('Airport'),
      'amusement_park' => $this->t('Amusement park'),
      'aquarium' => $this->t('Aquarium'),
      'art_gallery' => $this->t('Art gallery'),
      'atm' => $this->t('Atm'),
      'bakery' => $this->t('Bakery'),
      'bank' => $this->t('Bank'),
      'bar' => $this->t('Bar'),
      'beauty_salon' => $this->t('Beauty salon'),
      'bicycle_store' => $this->t('Bicycle store'),
      'book_store' => $this->t('Book store'),
      'bowling_alley' => $this->t('Bowling alley'),
      'bus_station' => $this->t('Bus station'),
      'cafe' => $this->t('Cafe'),
      'campground' => $this->t('Campground'),
      'car_dealer' => $this->t('Car dealer'),
      'car_rental' => $this->t('Car rental'),
      'car_repair' => $this->t('Car repair'),
      'car_wash' => $this->t('Car wash'),
      'casino' => $this->t('Casino'),
      'cemetery' => $this->t('Cemetery'),
      'church' => $this->t('Church'),
      'city_hall' => $this->t('City hall'),
      'clothing_store' => $this->t('Clothing store'),
      'convenience_store' => $this->t('Convenience store'),
      'courthouse' => $this->t('Courthouse'),
      'dentist' => $this->t('Dentist'),
      'department_store' => $this->t('Department store'),
      'doctor' => $this->t('Doctor'),
      'electrician' => $this->t('Electrician'),
      'electronics_store' => $this->t('Electronics store'),
      'embassy' => $this->t('Embassy'),
      'fire_station' => $this->t('Fire station'),
      'florist' => $this->t('Florist'),
      'funeral_home' => $this->t('Funeral home'),
      'furniture_store' => $this->t('Furniture store'),
      'gas_station' => $this->t('Gas station'),
      'grocery_or_supermarket' => $this->t('Grocery or supermarket'),
      'gym' => $this->t('Gym'),
      'hair_care' => $this->t('Hair care'),
      'hardware_store' => $this->t('Hardware store'),
      'hindu_temple' => $this->t('Hindu temple'),
      'home_goods_store' => $this->t('Home goods store'),
      'hospital' => $this->t('Hospital'),
      'insurance_agency' => $this->t('Insurance agency'),
      'jewelry_store' => $this->t('Jewelry store'),
      'laundry' => $this->t('Laundry'),
      'lawyer' => $this->t('Lawyer'),
      'library' => $this->t('Library'),
      'liquor_store' => $this->t('Liquor store'),
      'local_government_office' => $this->t('Local government office'),
      'locksmith' => $this->t('Locksmith'),
      'lodging' => $this->t('Lodging'),
      'meal_delivery' => $this->t('Meal delivery'),
      'meal_takeaway' => $this->t('Meal takeaway'),
      'mosque' => $this->t('Mosque'),
      'movie_rental' => $this->t('Movie rental'),
      'movie_theater' => $this->t('Movie theater'),
      'moving_company' => $this->t('Moving company'),
      'museum' => $this->t('Museum'),
      'night_club' => $this->t('Night club'),
      'painter' => $this->t('Painter'),
      'park' => $this->t('Park'),
      'parking' => $this->t('Parking'),
      'pet_store' => $this->t('Pet store'),
      'pharmacy' => $this->t('Pharmacy'),
      'physiotherapist' => $this->t('Physiotherapist'),
      'plumber' => $this->t('Plumber'),
      'police' => $this->t('Police'),
      'post_office' => $this->t('Post office'),
      'real_estate_agency' => $this->t('Real estate agency'),
      'restaurant' => $this->t('Restaurant'),
      'roofing_contractor' => $this->t('Roofing contractor'),
      'rv_park' => $this->t('Rv park'),
      'school' => $this->t('School'),
      'shoe_store' => $this->t('Shoe store'),
      'shopping_mall' => $this->t('Shopping mall'),
      'spa' => $this->t('Spa'),
      'stadium' => $this->t('Stadium'),
      'storage' => $this->t('Storage'),
      'store' => $this->t('Store'),
      'subway_station' => $this->t('Subway station'),
      'synagogue' => $this->t('Synagogue'),
      'taxi_stand' => $this->t('Taxi stand'),
      'train_station' => $this->t('Train station'),
      'transit_station' => $this->t('Transit station'),
      'travel_agency' => $this->t('Travel agency'),
      'university' => $this->t('University'),
      'veterinary_care' => $this->t('Veterinary care'),
      'zoo' => $this->t('Zoo'),
    ];
    $form['#attached']['library'][] = 'nearby_places_search/nearby_places_search_admin';

    $form['nearby_places_search_types'] = [
      '#title' => $this->t('Location Types:<br/><br/>'),
      '#type' => 'checkboxes',
      '#options' => $types,
      '#default_value' => $get_default,
      '#attributes' => ['class' => ['adm-chkbox']],
    ];
    $form['nearby_places_search_auth_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Google API Authentication Method'),
      '#description' => $this->t('Google API Authentication Method'),
      '#default_value' => $config->get('nearby_places_search_auth_method') ?: 1,
      '#options' => [
        1 => $this->t('API Key'),
        2 => $this->t('Google Maps API for Work'),
      ],
    ];

    $form['nearby_places_search_apikey'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Google Maps API Key'),
      '#description' => $this->t('Obtain a Google Maps Javascript API key at <a href="@link">@link</a>', [
        '@link' => 'https://developers.google.com/maps/documentation/javascript/get-api-key',
      ]),
      '#default_value' => $config->get('nearby_places_search_apikey') ?: '',
      '#required' => FALSE,
      '#states' => [
        'visible' => [
          ':input[name="nearby_places_search_auth_method"]' => ['value' => 1],
        ],
      ],
    ];

    $form['nearby_places_search_client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Google Maps API for Work: Client ID'),
      '#description' => $this->t('For more information, visit: <a href="@link">@link</a>', [
        '@link' => 'https://developers.google.com/maps/documentation/javascript/get-api-key#client-id',
      ]),
      '#default_value' => $config->get('nearby_places_search_client_id') ?: '',
      '#required' => FALSE,
      '#states' => [
        'visible' => [
          ':input[name="nearby_places_search_auth_method"]' => ['value' => 2],
        ],
      ],
    ];

    $form['nearby_places_search_private_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Google Maps API for Work: Private/Signing Key'),
      '#description' => $this->t('For more information, visit: <a href="@link">@link</a>', [
        '@link' => 'https://developers.google.com/maps/documentation/business/webservices/auth#how_do_i_get_my_signing_key',
      ]),
      '#default_value' => $config->get('nearby_places_search_private_key') ?: '',
      '#required' => FALSE,
      '#states' => [
        'visible' => [
          ':input[name="nearby_places_search_auth_method"]' => ['value' => 2],
        ],
      ],
    ];

    $form['nearby_places_search_latitude'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Location : Latitude'),
      '#default_value' => $config->get('nearby_places_search_latitude'),
      '#size' => 40,
      '#maxlength' => 255,
      '#description' => $this->t('The location&#39s latitude which you wish to search from'),
      '#required' => TRUE,
    ];

    $form['nearby_places_search_longitude'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Location : Longitude'),
      '#default_value' => $config->get('nearby_places_search_longitude'),
      '#size' => 40,
      '#maxlength' => 255,
      '#description' => $this->t('The location&#39s longitude which you wish to search from'),
      '#required' => TRUE,
    ];

    $form['nearby_places_search_location_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Location Name'),
      '#default_value' => $config->get('nearby_places_search_location_title'),
      '#size' => 40,
      '#maxlength' => 255,
      '#description' => $this->t('The default location name for above latitude and longitude.'),
      '#required' => TRUE,
    ];

    $form['nearby_places_search_radius'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Radius'),
      '#default_value' => $config->get('nearby_places_search_radius'),
      '#size' => 5,
      '#maxlength' => 5,
      '#description' => $this->t('The radius in meters, from your search start point. Maximum is 50000.'),
      '#required' => TRUE,
    ];
    return parent::buildForm($form, $form_state);
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
    $this->config('nearby_places_search.settings')
      ->set('nearby_places_search_types', $form_state->getValue('nearby_places_search_types'))
      ->set('nearby_places_search_auth_method', $form_state->getValue('nearby_places_search_auth_method'))
      ->set('nearby_places_search_apikey', $form_state->getValue('nearby_places_search_apikey'))
      ->set('nearby_places_search_client_id', $form_state->getValue('nearby_places_search_client_id'))
      ->set('nearby_places_search_private_key', $form_state->getValue('nearby_places_search_private_key'))
      ->set('nearby_places_search_latitude', $form_state->getValue('nearby_places_search_latitude'))
      ->set('nearby_places_search_longitude', $form_state->getValue('nearby_places_search_longitude'))
      ->set('nearby_places_search_location_title', $form_state->getValue('nearby_places_search_location_title'))
      ->set('nearby_places_search_radius', $form_state->getValue('nearby_places_search_radius'))
      ->save();
  }

}
