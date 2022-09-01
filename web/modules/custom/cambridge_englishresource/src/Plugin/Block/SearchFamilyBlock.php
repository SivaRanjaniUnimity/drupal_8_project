<?php

namespace Drupal\cambridge_englishresource\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Search family Template' block.
 *
 * @Block(
 *   id = "searchfamily_template_block",
 *   admin_label = @Translation("Search Family Template")
 * )
 */
class SearchFamilyBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
      dpm(11);
      $query = \Drupal::entityQuery('node')
      ->condition('type', 'family')
      // ->condition('title', '%' . $family . '%', 'LIKE')
      ->condition('status', 1)
      ->range(0, 10);
      $results = $query->execute();
      $nodes =  \Drupal\node\Entity\Node::loadMultiple($results);
      $output = array();
      if(count($nodes)>0){            
          foreach($nodes as $node){
          $title = $node->get('title')->value;
          $nid= $node->get('nid')->value;
          $styled_image_url = ImageStyle::load('thumbnail')->buildUrl($node->field_image->entity->getFileUri());
          $output[] = array("nid"=>$nid,"title"=>$title,"image_url"=>$styled_image_url);
          }            
      }
      dpm($output);
    $renderable = [
      '#theme' => 'search_family',
      '#data' => $output,
    ];

    return $renderable;
  }

  public function getCacheMaxAge() {
    return 0;
}

}