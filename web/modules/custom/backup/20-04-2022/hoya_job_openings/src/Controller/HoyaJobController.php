<?php

namespace Drupal\hoya_job_openings\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\taxonomy\Entity\Term;

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
        $termarray = array();
        
        if (!empty($type)) {
        $nidarr = array();   
       
            if($type == 'designation'){
            $results = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'job_openings', 'field_role' => $tid]);
            }
            else if($type == 'location'){
                $results = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'job_openings', 'field_country' => $tid]);

                foreach($tid as $term_id){
                    $term_obj = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($term_id);
                    $short_name = $term_obj->get('field_short_name')->value;
                    $termname = $term_obj->getName();
                    $termarray[] = array("id" =>$short_name, "value" => $termname,"name" =>0);
                }
            }
            else{
                $results = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'job_openings']);
            }
            if (!empty($results))
            {
                $entity_type = 'node';
                $view_mode = 'full';
                foreach($results as $node){
                    $output[] = render(\Drupal::entityTypeManager()->getViewBuilder($entity_type)->view($node, $view_mode));
                    $term_id = $node->get('field_role')->getString();
                    $available_roles[] = $node->get('field_role')->getString();
                   
                }             
            }
        
        return new JsonResponse(array("output"=>$output,"available_roles"=>$available_roles,"dataset"=>$termarray));
        // return new JsonResponse(array("output"=>'11'));
        }    
        return new JsonResponse(array("output"=>$type));
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
