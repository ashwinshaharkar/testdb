<?php

namespace Drupal\gcss\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SettingsForm.
 *
 * @package Drupal\gcss\Form
 */
class GcssForm extends ConfigFormBase
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

        $config = $this->config('gcss.settings');

        $form['gcss_fieldset_merchant'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Merchant account configuration'),
        ];

        $form['gcss_fieldset_merchant']['gcss_merchant_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Merchant ID:'),
            '#default_value' => $config->get('gcss_merchant_id'),
            '#size' => 60,
            '#maxlength' => 255,
            '#description' => '',
            '#required' => true,
        ];

        $form['gcss_fieldset_client'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('OAuth2 authentication configurations'),
        ];

        $form['gcss_fieldset_client']['gcss_help'] = [
            '#type' => 'item',
            '#markup' => t('For OAuth configuration please check <a target="_blank" href="@link">@link</a>', ['@link' => 'https://developers.google.com/adwords/api/docs/guides/authentication#webapp']),
        ];

        $form['gcss_fieldset_client']['gcss_client_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Client ID:'),
            '#default_value' => $config->get('gcss_client_id'),
            '#size' => 100,
            '#maxlength' => 255,
            '#description' => '',
            '#required' => true,
        ];

        $form['gcss_fieldset_client']['gcss_client_secret_key'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Client secret key:'),
            '#default_value' => $config->get('gcss_client_secret_key'),
            '#size' => 100,
            '#maxlength' => 255,
            '#description' => '',
            '#required' => true,
        ];

        $form['gcss_fieldset_client']['gcss_client_auth_config_file_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Client oauth config file name:'),
            '#default_value' => $config->get('gcss_client_auth_config_file_name'),
            '#size' => 100,
            '#maxlength' => 255,
            '#description' => 'e.g. client_secrets.json<br/>Download client auth config file and upload it under Drupal root directory i.e <b>' . DRUPAL_ROOT . '\</b>',
            '#required' => true,
        ];

        $form['gcss_fieldset_client']['gcss_developer_api_key'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Developer / API key:'),
            '#default_value' => $config->get('gcss_developer_api_key'),
            '#size' => 100,
            '#maxlength' => 255,
            '#description' => '',
            '#required' => true,
        ];

        $form['gcss_fieldset_client']['gcss_oauth2_callback_url'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Oauth2 callback URL:'),
            '#default_value' => $base_url . '/admin/oauth2callback',
            '#size' => 100,
            '#maxlength' => 255,
            '#attributes' => array('readonly' => 'readonly'),
            '#description' => 'Set this url as a callback url for your oauth2 authentication',
            '#required' => true,
        ];

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        parent::submitForm($form, $form_state);
        $this->config('gcss.settings')
            ->set('gcss_merchant_id', $form_state->getValue('gcss_merchant_id'))
            ->set('gcss_client_id', $form_state->getValue('gcss_client_id'))
            ->set('gcss_client_secret_key', $form_state->getValue('gcss_client_secret_key'))
            ->set('gcss_client_auth_config_file_name', $form_state->getValue('gcss_client_auth_config_file_name'))
            ->set('gcss_developer_api_key', $form_state->getValue('gcss_developer_api_key'))
            ->set('gcss_oauth2_callback_url', $form_state->getValue('gcss_oauth2_callback_url'))
            ->save();
    }

}
