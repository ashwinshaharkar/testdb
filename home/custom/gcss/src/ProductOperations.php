<?php

namespace Drupal\gcss;

use \Drupal\node\Entity\Node;

class ProductOperations
{
    public static function UpdateProducts($products, &$context)
    {
        $message = '';
        foreach ($products as $product) {
            $node = Node::create([
                'type' => 'google_products',
                'title' => $product['title'],
                'body' => $product['body'],
                'promote' => 0,
                'field_gproduct_link' => $product['field_gproduct_link'],
                'field_gproduct_gtin' => $product['field_gproduct_gtin'],
                'field_gproduct_id' => $product['field_gproduct_id'],
                'field_gproduct_brand' => $product['field_gproduct_brand'],
                'field_gproduct_image_link' => $product['field_gproduct_image_link'],
                'field_gproduct_price' => $product['field_gproduct_price'],
                'field_gproduct_currency' => $product['field_gproduct_currency'],
            ]);
            $node->field_gproduct_merchant->setValue(['target_id' => $product['field_gproduct_merchant']]);
            $node->save();
        }

        $context['message'] = $message;
        $context['results'] = $products;
    }

    public static function UpdateProductsFinishedCallback($success, $results, $operations)
    {
        // The 'success' parameter means no fatal PHP errors were detected. All
        // other error management should be handled using 'results'.
        if ($success) {
            $message = \Drupal::translation()->formatPlural(
                count($results),
                'One product processed.', '@count products processed.'
            );
        } else {
            $message = t('Finished with an error.');
        }
        drupal_set_message($message);
    }

}
