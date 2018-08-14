<?php

namespace Drupal\gcss\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Google_Client;
use Google_Service_ShoppingContent;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class FetchProductsForm.
 *
 * @package Drupal\gcss\Form
 */
class FetchProductsForm extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'fetch_products_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['fetch_products'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Fetch Products'),
        );
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        global $base_url;
        $products_arr = array();

        // Start a session to persist credentials.
        if (!isset($_SESSION)) {
            session_start();
        }

        $config = \Drupal::config('gcss.settings');
        $client_auth_file_name = $config->get('gcss_client_auth_config_file_name') ?: '/client_secrets.json';
        $client_oauthCallbackUrl = $config->get('gcss_oauth2_callback_url');
        $clientId = $config->get('gcss_client_id') ?: '';
        $clientSecret = $config->get('gcss_client_secret_key') ?: '';
        $developerKey = $config->get('gcss_developer_api_key') ?: '';
        $merchantID = $config->get('gcss_merchant_id') ?: '';

        if ($clientId && $clientSecret && $developerKey && $merchantID) {
            // Create the client object and set the authorization configuration
            // from the client_secretes.json you downloaded from the developer console.
            $client = new Google_Client();
            $client->setAuthConfig(DRUPAL_ROOT . '\\' . $client_auth_file_name);
            $client->setAccessType('online'); // default: offline
            $client->setIncludeGrantedScopes(true); // incremental auth
            $client->setApplicationName('Content API for Shopping Samples');
            $client->setScopes(Google_Service_ShoppingContent::CONTENT);
            $client->setClientId($clientId);
            $client->setClientSecret($clientSecret);
            $client->setDeveloperKey($developerKey); // API key
            $client->setIncludeGrantedScopes(true); // incremental auth

            // If the user has already authorized this app then get an access token
            // else redirect to ask the user to authorize access to Google Analytics.
            if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
                // Set the access token on the client.
                $client->setAccessToken($_SESSION['access_token']);
                $service = new Google_Service_ShoppingContent($client);
                try {
                    $products = $service->products->listProducts($merchantID);
                    $parameters = array();
                    $i = 0;
                    while (!empty($products->getResources())) {
                        foreach ($products->getResources() as $product) {
                            //check the prodcut is already exists or not in drupal
                            $connection = \Drupal::database();
                            $query = $connection->query("select count(field_gproduct_id_value) as cnt from node__field_gproduct_id where field_gproduct_id_value ='" . $product->getId() . "'");
                            $result = $query->fetchAll();
                            if ($result[0]->cnt == 0) {
                                $products_arr[$i]['title'] = $product->getTitle();
                                $products_arr[$i]['body'] = $product->getDescription();
                                $products_arr[$i]['field_gproduct_link'] = $product->getLink();
                                $products_arr[$i]['field_gproduct_gtin'] = $product->getGtin();
                                $products_arr[$i]['field_gproduct_id'] = $product->getId();
                                $products_arr[$i]['field_gproduct_brand'] = $product->getBrand();
                                $products_arr[$i]['field_gproduct_image_link'] = $product->getImageLink();
                                $products_arr[$i]['field_gproduct_price'] = $product->getPrice()->getValue();
                                $products_arr[$i]['field_gproduct_currency'] = $product->getPrice()->getCurrency();
                            }
                            $i++;
                        }
                        //$products_arr = $products->getResources();
                        if (!empty($products->getNextPageToken())) {
                            break;
                        }
                        $parameters['pageToken'] = $products->nextPageToken;
                        $products = $service->products->listProducts($merchantID, $parameters);
                    }
                } catch (\Google_Service_Exception $e) {
                    $response = new RedirectResponse($client_oauthCallbackUrl, 302);
                    $response->send();
                }
            } else {
                $response = new RedirectResponse($client_oauthCallbackUrl, 302);
                $response->send();
            }
        }

        $batch = array(
            'title' => t('Fetching products...'),
            'operations' => array(
                array(
                    '\Drupal\gcss\ProductOperations::FetchProducts',
                    array($products_arr),
                ),
            ),
            'finished' => '\Drupal\gcss\ProductOperations::FetchProductsFinishedCallback',
        );
        batch_set($batch);
    }

}
