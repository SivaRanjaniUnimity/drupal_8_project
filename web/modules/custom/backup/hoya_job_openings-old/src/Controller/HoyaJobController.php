<?php

namespace Drupal\hoya_job_openings\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Defines HoyaJobController class.
 */
class HoyaJobController extends ControllerBase
{

    /**
     * {@inheritdoc}
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
       
            if($type == 'designation'){
            $results = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'job_openings', 'field_role' => $tid]);
            }
            else if($type == 'location'){
                $results = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'job_openings', 'field_country' => $tid]);
            }
            if (!empty($results))
            {
                $entity_type = 'node';
                $view_mode = 'full';
                foreach($results as $node){
                    $output[] = render(\Drupal::entityTypeManager()->getViewBuilder($entity_type)->view($node, $view_mode));
                    $available_roles[] = $node->get('field_role')->getString();
                }             
            }
        
        return new JsonResponse(array("output"=>$output, "available_roles" => $available_roles));
        }    
        return new JsonResponse(array("output"=>''));
    }

    /**
     * {@inheritdoc}
     */
    public function getoffice_details(Request $request)
    {
        $service = \Drupal::service('hoya_job_openings.services');    
        $office_details = $service->fetch_office_detail();
        return new JsonResponse(array("output"=>$office_details));
    }

}
