<?php

namespace Drupal\gcss;


use Drupal\node\Entity\Node;

class ProductOperations {

  public static function FetchProducts($nids, &$context){
    $message = '';
    $results = array();
    foreach ($nids as $nid) {
      $results[] = array();
    }
    $context['message'] = $message;
    $context['results'] = $results;
  }

  function FetchProductsFinishedCallback($success, $results, $operations) {
    // The 'success' parameter means no fatal PHP errors were detected. All
    // other error management should be handled using 'results'.
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'One products processed.', '@count productss processed.'
      );
    }
    else {
      $message = t('Finished with an error.');
    }
    drupal_set_message($message);
  }
}