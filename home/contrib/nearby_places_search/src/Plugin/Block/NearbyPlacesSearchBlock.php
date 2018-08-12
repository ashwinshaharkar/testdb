<?php

namespace Drupal\nearby_places_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides Nearby Places Search Block.
 *
 * @Block(
 * id = "nearby_places_search_block",
 * admin_label = @Translation("Nearby Places Search"),
 * category = @Translation("Blocks")
 * )
 */
class NearbyPlacesSearchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['form'] = \Drupal::formBuilder()->getForm('Drupal\nearby_places_search\Form\NearbyPlacesSearchBlockForm');
    return $build;
  }

}
