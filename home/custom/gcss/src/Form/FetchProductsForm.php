<?php

namespace Drupal\gcss\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

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
        $nids = array();
        $batch = array(
            'title' => t('Fetching products...'),
            'operations' => array(
                array(
                    '\Drupal\gcss\ProductOperations::FetchProducts',
                    array($nids),
                ),
            ),
            'finished' => '\Drupal\gcss\ProductOperations::FetchProductsFinishedCallback',
        );
        batch_set($batch);
    }

}
