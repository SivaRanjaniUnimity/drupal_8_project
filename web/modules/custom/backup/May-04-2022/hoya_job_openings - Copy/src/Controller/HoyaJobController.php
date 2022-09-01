<?php
/**
 * HoyaJobController File Doc Comment
 * php version 7.4.26

 * @category Components
 * @package  Drupal
 * @author   Hoya <hso-ghq-hr@hoya.com>
 * @license  https://github.com/hoyasurgicaloptics/LICENSE.txt hoyasurgicaloptics  
 * @link     https://hoyasurgicalopticsdev.prod.acquia-sites.com/ *
 */
namespace Drupal\hoya_job_openings\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Defines HoyaJobController class.
 *
 * @category Components
 * @package  Drupal
 * @author   Hoya <hso-ghq-hr@hoya.com>
 * @license  https://github.com/hoyasurgicaloptics/LICENSE.txt hoyasurgicaloptics  
 * @link     https://hoyasurgicalopticsdev.prod.acquia-sites.com/ *
 */
class HoyaJobController extends ControllerBase
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
        $type = \Drupal::request()->request->get('type'); 
        $filterdata = \Drupal::request()->request->get('filterdata'); 
        $tid = \Drupal::request()->request->get('filter_id'); 
        $output = array();
        $available_roles = array();
        
        if (!empty($tid)) {
            $nidarr = array();   
            $dataset = array(); 
       
            if ($type == 'designation') {
                $info = ['type' => 'job_openings', 'field_role' => $tid];
                $results = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties($info);
            } else if ($type == 'location') {
                $info = ['type' => 'job_openings', 'field_country' => $tid];
                $results = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties($info);
                foreach ($tid as $term_id) {
                    $term_obj = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($term_id);
                    $short_name = $term_obj->get('field_short_name')->value;
                    $location = $term_obj->getName();
                    $dataset[] = array("id" =>$short_name, "value" => $location,"name" =>0);
                }
            }
            if (!empty($results)) {
                $entity_type = 'node';
                $view_mode = 'teaser';
                foreach ($results as $node) {
                    $render_info = \Drupal::entityTypeManager()->getViewBuilder($entity_type)->view($node, $view_mode);
                    $output[] = render($render_info);
                    $available_roles[] = $node->get('field_role')->getString();
                }             
            }
            
            $return_data = array("output"=>$output, "available_roles" => $available_roles, "dataset" => $dataset);
            return new JsonResponse($return_data);
        }    
        return new JsonResponse(array("output"=>''));
    }

    /**
     * Function getdetail
     *
     * @param Symfony\Component\HttpFoundation\Request $request The incoming request.
     * 
     * @return Symfony\Component\HttpFoundation\JsonRespons
     *  The response containing the entity with its accessible fields.
     */
    public function getOfficeDetails(Request $request)
    {
        $service = \Drupal::service('hoya_job_openings.services');    
        $office_details = $service->fetchOfficeDetail();
        return new JsonResponse(array("output"=>$office_details));
    }

}
