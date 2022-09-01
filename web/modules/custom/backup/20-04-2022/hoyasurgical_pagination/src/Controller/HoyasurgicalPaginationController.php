<?php

namespace Drupal\hoyasurgical_pagination\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Defines HoyasurgicalPaginationController class.
 */
class HoyasurgicalPaginationController extends ControllerBase
{

    /**
     * {@inheritdoc}
     */
    public function getdetail(Request $request)
    {
        $path_value = \Drupal::request()->request->get('current_path'); 
   
        if (!empty($path_value)) {
            $node_path = \Drupal::service('path_alias.manager')->getPathByAlias($path_value);
            if (preg_match('/node\/(\d+)/', $node_path, $matches)) {
                $nid = $matches[1];
                $entity_type = 'node';
                $view_mode = 'full';

                $node = \Drupal::entityTypeManager()->getStorage($entity_type)->load($nid);
                $output = render(\Drupal::entityTypeManager()->getViewBuilder($entity_type)->view($node, $view_mode));

                $result = array("output"=>$output);
                return new JsonResponse($result);
            }
        }
        return new JsonResponse(array("output"=>null));
    }

}
