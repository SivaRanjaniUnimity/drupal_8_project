<?php

namespace Drupal\hoya_job_openings;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * JobOpeningServices service.
 */
class JobOpeningServices {

  /**
   * Method description.
   */
  public function fetchTermdata($vocabulary) {
    // @DCG place your code here.
    
    $vid = $vocabulary;
    $options = [];
    $options_attributes = [];
    $termid_arr = [];
    $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
    foreach ($terms as $term) {
    $term_data[] = array(
      'id' => $term->tid,
      'name' => $term->name
    );

    $options[$term->tid] = $term->name;
  
    
    if($vocabulary == 'designation'){
      $results = $this->resultCount("job_openings","field_role",$term->tid);
    }
    else{
      $results = $this->resultCount("job_openings","field_country",$term->tid);
    }
   
    $results_count = count($results);
    $termid_arr[$term->tid] = $results_count; 
   
    }        
    $options_result = array("options" => $options, "option_attribute" =>$options_attributes,"termid_arr"=>$termid_arr);
    return $options_result;

  }


    /**
   * Method description.
   */
  public function fetch_office_detail() {
    // @DCG place your code here.
    $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree("office_location");
    $result = array();
    $result1 = array();
    $result2 = array();

    foreach ($terms as $term) {
      $term_obj = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($term->tid);
      $url = "";
      $id = $term->tid;
      $name = $term->name;
      $short_name = $term_obj->get('field_short_name')->value;
      $latitude = $term_obj->get('field_latitude')->value;
      $longitude = $term_obj->get('field_longitude')->value;
     
      $results = $this->resultCount("job_openings","field_country",$id);

      $results_count = count($results);
      if(!empty($latitude)){

      $result1[] = array("id"=>$short_name,"name"=>$name,"term_id"=>$id,"lat"=>$latitude,"long"=>$longitude,"number"=>$results_count);
      
      $result2[] = array("id"=>$short_name,"value"=>$name,"number"=>$results_count);     

      }
      $result['data']=$result1;
      $result['dataset']=$result2;     
    }
    return $result;
  }


  public function getAllResult()
    {
        $results = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'job_openings']);
        $entity_type = 'node';
        $view_mode = 'full';
        $output ="";
        if(!empty($results)){
          foreach($results as $node){
            $output .= render(\Drupal::entityTypeManager()->getViewBuilder($entity_type)->view($node, $view_mode));
        } 
        }
        
        
        return $output;
        }  

  public function resultCount($contenttype=null, $fieldname=null, $fieldvalue=null){
    $results = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => $contenttype, $fieldname => $fieldvalue]);
    return $results;
  }

}
