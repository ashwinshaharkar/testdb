<?php

namespace Drupal\gcss\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Google_Client;
use Google_Service_ShoppingContent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Drupal\file\Entity\File;

/**
 * Class UpdateProductsForm.
 *
 * @package Drupal\gcss\Form
 */
class UpdateProductsForm extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'update_products_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $merchant_accounts = array();
        $merchant_arr = $this->selectMerchantAccounts();
        foreach ($merchant_arr as $merchant) {
            $nid = $merchant->nid;
            $mtitle = $merchant->title;
            $mid = $merchant->field_merchant_id_value;
            $merchant_accounts[$nid] = $mid . ' - ' . $mtitle;
        }
        $form['select_merchant_account'] = [
            '#type' => 'select',
            '#title' => $this->t('Please select merchant account'),
            '#required' => true,
            '#multiple' => false,
            '#options' => $merchant_accounts,
        ];
        $form['update_products'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Update Products'),
        );
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        global $base_url;
        $service_key_file_path = str_replace('\\', '/', \Drupal::service('file_system')->realpath("private://") . '/service_keys/');
        $form_state_arr[] = $form_state->getValue('select_merchant_account');
        //entity_delete_multiple('node', \Drupal::entityQuery('node')->execute());echo "done";die;
        foreach ($form_state_arr as $key => $value) {
            $product_details = $products_arr = $mer_arr = array();
            $account_arr = $this->selectMerchantAccounts($value);
            foreach ($account_arr as $merchant) {
                $nid = $merchant->nid;
                $mid = $merchant->field_merchant_id_value;
                $skey_fid = $merchant->field_service_key_target_id;
                $skey_file = File::load($skey_fid);
                $skey_filename_path = $service_key_file_path . $skey_file->getFilename(); //or maybe $file->filename
                if (file_exists($skey_filename_path)) {
                    $client = new Google_Client();
                    $client->setAccessType('online'); // default: offline
                    $client->setIncludeGrantedScopes(true); // incremental auth
                    $client->setApplicationName('Content API for Shopping Samples');
                    $client->setAuthConfig("$skey_filename_path");
                    $client->setScopes(Google_Service_ShoppingContent::CONTENT);

                    $service = new Google_Service_ShoppingContent($client);
                    $parameters = array('maxResults' => 250);
                    $products = $service->products->listProducts($mid, $parameters);
                    $i = 1;
                    //$count = 0;
                    //We can fetch all items in a loop. We limit to looping just 10
                    //times as it may take a long time to finish if we have many products.
                    //while (!empty($products->getResources()) && $count++ < 10) {
                    while (!empty($products->getResources())) {
                        foreach ($products->getResources() as $product) {
                            $rec_cont = $this->getProductCount($product->getId(), $nid);
                            if ($rec_cont == 0) {
                                $products_arr[$i]['title'] = $product->getTitle();
                                $products_arr[$i]['body'] = $product->getDescription();
                                $products_arr[$i]['field_gproduct_link'] = $product->getLink();
                                $products_arr[$i]['field_gproduct_gtin'] = $product->getGtin();
                                $products_arr[$i]['field_gproduct_id'] = $product->getId();
                                $products_arr[$i]['field_gproduct_brand'] = $product->getBrand();
                                $products_arr[$i]['field_gproduct_image_link'] = $product->getImageLink();
                                $products_arr[$i]['field_gproduct_price'] = $product->getPrice()->getValue();
                                $products_arr[$i]['field_gproduct_currency'] = $product->getPrice()->getCurrency();
                                $products_arr[$i]['field_gproduct_merchant'] = $nid;
                            }
                            $i++;
                        }
                        // If the result has a nextPageToken property then there are more pages available to fetch
                        if (empty($products->getNextPageToken())) {
                            break;
                        }
                        // You can fetch the next page of results by setting the pageToken
                        // parameter with the value of nextPageToken from the previous result.
                        $parameters['pageToken'] = $products->nextPageToken;
                        $products = $service->products->listProducts($mid, $parameters);
                    }

                } else {
                    $response = new RedirectResponse($base_url . '/admin/config/gcss/update-products', 302);
                    $response->send();
                    $message = "Merchant account's service key file is not found.!";
                    drupal_set_message(t($message), 'error', true);
                }
            }
        }
        
        $batch = array(
            'title' => t('Updating products...'),
            'operations' => array(
                array(
                    '\Drupal\gcss\ProductOperations::UpdateProducts',
                    array($products_arr),
                ),
            ),
            'finished' => '\Drupal\gcss\ProductOperations::UpdateProductsFinishedCallback',
        );
        batch_set($batch);
    }

    /**
     * {@inheritdoc}
     */
    public function selectMerchantAccounts($nid = '')
    {
        $connection = \Drupal::database();
        $query = $connection->select('node_field_data', 'n');
        $query->join('node__field_merchant_id', 'mid', 'mid.entity_id = n.nid');
        $query->join('node__field_service_key', 'skey', 'skey.entity_id = n.nid');

        $sessions = $query
            ->fields('n', ['nid', 'title', 'created'])
            ->fields('mid', ['field_merchant_id_value'])
            ->fields('skey', ['field_service_key_target_id'])
            ->condition('n.status', 1)
            ->orderBy('n.created', 'ASC');
        if ($nid != '') {
            $query->condition('n.nid', $nid);
        }
        $merchant_arr = $sessions->execute()->fetchAll();

        return $merchant_arr;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductCount($pid, $nid)
    {
        $connection = \Drupal::database();
        $query = $connection->select('node_field_data', 'n');
        $query->join('node__field_gproduct_merchant', 'mid', 'mid.entity_id = n.nid');
        $query->join('node__field_gproduct_id', 'pid', 'pid.entity_id = n.nid');

        $sessions = $query
            ->fields('n', ['nid', 'title', 'created'])
            ->fields('mid', ['field_gproduct_merchant_target_id'])
            ->fields('pid', ['field_gproduct_id_value'])
            ->condition('mid.field_gproduct_merchant_target_id', $nid)
            ->condition('pid.field_gproduct_id_value', $pid)
            ->execute();

        $product_arr = $sessions->fetchAll();

        return count($product_arr);
    }

}
