<?php
/**
 * HoyasurgicalPaginationController File Doc Comment
 * php version 7.4.26

 * @category Components
 * @package  Drupal
 * @author   Hoya <hso-ghq-hr@hoya.com>
 * @license  https://github.com/hoyasurgicaloptics/LICENSE.txt hoyasurgicaloptics  
 * @link     https://hoyasurgicalopticsdev.prod.acquia-sites.com/ *
 */

namespace Drupal\hoyasurgical_pagination\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Defines HoyasurgicalPaginationController class.
 *
 * @category Components
 * @package  Drupal
 * @author   Hoya <hso-ghq-hr@hoya.com>
 * @license  https://github.com/hoyasurgicaloptics/LICENSE.txt hoyasurgicaloptics  
 * @link     https://hoyasurgicalopticsdev.prod.acquia-sites.com/ *
 */
class HoyasurgicalPaginationController extends ControllerBase
{
    /**
     * Function getdetail
     *
     * @param Symfony\Component\HttpFoundation\Request $request The incoming request.
     * 
     * @return Symfony\Component\HttpFoundation\JsonRespons
     *  The response containing the entity with its accessible fields.
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
                $viewbuilder = \Drupal::entityTypeManager()->getViewBuilder($entity_type)->view($node, $view_mode);
                $output = render($viewbuilder);

                $result = array("output"=>$output);
                return new JsonResponse($result);
            }
        }
        return new JsonResponse(array("output"=>null));
    }

}
