<?php

namespace Drupal\gcss\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SettingsForm.
 *
 * @package Drupal\gcss\Form
 */
class MerchantForm extends ConfigFormBase
{

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return [
            'gcss.settings',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'gcss_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        global $base_url;

        $config = $this->config('gcss_merchant.settings');

        $i = 0;
        $name_field = $form_state->get('num_names');
        $form['#tree'] = true;
        
        if (empty($name_field)) {
            $name_field = $form_state->set('num_names',1);
        }
        for ($i = 0; $i < $name_field; $i++) {
            $form['gcss_fieldset_merchant'][$i] = [
                '#type' => 'fieldset',
                '#title' => $this->t('Merchant accounts'),
                '#prefix' => '<div id="gcss-fieldset-wrapper">',
                '#suffix' => '</div>',
            ];
            $form['gcss_fieldset_merchant'][$i]['gcss_merchantid'] = [
                '#type' => 'textfield',
                '#title' => t('Merchant ID'),
            ];
            $form['gcss_fieldset_merchant'][$i]['gcss_servicekey'] = [
                '#type' => 'textfield',
                '#title' => t('Service account key file name'),
                '#default_value' => $i,
            ];
            if ($i > 0) {
                $form['gcss_fieldset_merchant'][$i]['remove'] = [
                    '#type' => 'submit',
                    '#value' => t('Remove one'),
                    '#submit' => array('::removeCallback'),
                    '#ajax' => [
                        'callback' => '::addmoreCallback',
                        'wrapper' => 'gcss-fieldset-wrapper',
                    ],
                ];
            }
           
        }

        $form['actions'] = [
            '#type' => 'actions',
        ];
        $form['actions']['add_name'] = [
            '#type' => 'submit',
            '#value' => t('Add one more'),
            '#submit' => array('::addOne'),
            '#ajax' => [
                'callback' => '::addmoreCallback',
                'wrapper' => 'gcss-fieldset-wrapper',
            ],
        ];
        $form_state->setCached(false);
        // if ($name_field > 1) {
        //     $form['gcss_fieldset_merchant']['actions']['remove_name'] = [
        //         '#type' => 'submit',
        //         '#value' => t('Remove one'),
        //         '#submit' => array('::removeCallback'),
        //         '#ajax' => [
        //             'callback' => '::addmoreCallback',
        //             'wrapper' => 'gcss-fieldset-wrapper',
        //         ],
        //     ];
        // }
        // $form['actions']['submit'] = [
        //     '#type' => 'submit',
        //     '#value' => $this->t('Submit'),
        // ];
        //return $form;
        return parent::buildForm($form, $form_state);
    }

    public function addOne(array &$form, FormStateInterface $form_state)
    {
        $name_field = $form_state->get('num_names');
        $add_button = $name_field + 1;
        $form_state->set('num_names', $add_button);
        $form_state->setRebuild();
    }

    public function addmoreCallback(array &$form, FormStateInterface $form_state)
    {
        $name_field = $form_state->get('num_names');
        return $form['gcss_fieldset_merchant'];
    }

    public function removeCallback(array &$form, FormStateInterface $form_state)
    {
        $name_field = $form_state->get('num_names');
        if ($name_field > 1) {
            $remove_button = $name_field - 1;
            $form_state->set('num_names', $remove_button);
        }
        $form_state->setRebuild();
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        echo "<pre>";
        print_r($form_state->getValues());die;
        $output = t('These people are coming to the picnic: @names', array(
            '@names' => implode(', ', $values),
        )
        );
        drupal_set_message($output);
    }

}
