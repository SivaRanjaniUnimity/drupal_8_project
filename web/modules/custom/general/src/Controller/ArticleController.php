<?php

namespace Drupal\general\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Defines ArticleController class.
 */
class ArticleController extends ControllerBase {

  /**
   * Display the markup.
   *
   * @return array
   *   Return markup array.
   */
  public function content(Request $request) {
    $path_value = \Drupal::request()->request->get('current_path'); 
   
    if(!empty($path_value)){
      $node_path = \Drupal::service('path_alias.manager')->getPathByAlias($path_value);
      if(preg_match('/node\/(\d+)/', $node_path, $matches)) {
        $nid = $matches[1];

        // $node = \Drupal\node\Entity\Node::load($matches[1]);
        // $prevnext = \Drupal::service('prevnext.service');
        // $previous_next = $prevnext->getPreviousNext($node);
        // $prev = $previous_next['prev'];
        // $next = $previous_next['next'];
        // $res_arr = array($prev,$nid,$next);
        // $res = implode("+",$res_arr);

        // $nid = 4;
        $entity_type = 'node';
        $view_mode = 'full';

        $node = \Drupal::entityTypeManager()->getStorage($entity_type)->load($nid);
        $output = render(\Drupal::entityTypeManager()->getViewBuilder($entity_type)->view($node, $view_mode));

        $result = array("output"=>$output);
        return new JsonResponse($result);
      }
    }
    return new JsonResponse( array("output"=>0));
  }

  public function get_custom_theme(){
    $data = [
      '#theme' => 'general_theme',
      '#data' => 'siva'
    ];
  }

}