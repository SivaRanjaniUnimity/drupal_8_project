<?php
/**
 * @file
 * Contains \Drupal\cambridge_englishresource\Controller\CambridgeEnglishConroller.
 */
namespace Drupal\cambridge_englishresource\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;

class CambridgeEnglishConroller extends ControllerBase {
    public function getData(Request $request)
    {
        $family = \Drupal::request()->request->get('family'); 
        // $query = \Drupal::entityQuery('node')
        // ->condition('type', 'page')
        // ->condition('field_some_field', 14);
        // $results = $query->execute();

        $query = \Drupal::entityQuery('node')
        ->condition('type', 'family')
        ->condition('title', '%' . $family . '%', 'LIKE')
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

        return new JsonResponse(array("output"=>$output));
    }

    function LinkFileToFamily(Request $request){
        $fileId = \Drupal::request()->request->get('fileId'); 
        $familyId = \Drupal::request()->request->get('familyId'); 

        $query = \Drupal::entityQuery('node')
        ->condition('type', 'file_resource')
        ->condition('nid', $fileId, '=')
        ->condition('status', 1)
        ->range(0, 10);
        $results = $query->execute();
        $nodes =  \Drupal\node\Entity\Node::loadMultiple($results);

        if(count($nodes)>0){            
            foreach($nodes as $node){
            $family_ids = $node->get('field_family_relationship')->getValue();
            $family_ids[]= array(
                'target_id' => "".$familyId.""
            );
            }   
            if(count($family_ids)>0){     
            $node->field_family_relationship= $family_ids;
            $node->save();  
            }       
        }

        $output[] = array("fileId"=>$fileId,"familyId"=>$familyId);
        return new JsonResponse(array("output"=>$output));
    }

    function UnlinkFileToFamily(Request $request){
        $fileId = \Drupal::request()->request->get('fileId'); 
        $familyId = \Drupal::request()->request->get('familyId'); 

        $query = \Drupal::entityQuery('node')
        ->condition('type', 'file_resource')
        ->condition('nid', $fileId, '=')
        ->condition('status', 1)
        ->range(0, 10);
        $results = $query->execute();
        $nodes =  \Drupal\node\Entity\Node::loadMultiple($results);

        if(count($nodes)>0){            
            foreach($nodes as $node){
            $family_ids = $node->get('field_family_relationship')->getValue();

            $value = $familyId ;
            foreach ( $family_ids as $name=> $info) {
                if ( $info['target_id'] == $familyId ) {
                    unset($family_ids [$name]);
                }
            }
            // $family_ids[]= array(
            //     'target_id' => "".$familyId.""
            // );
            }   
            if(count($family_ids)>0){     
            $node->field_family_relationship= $family_ids;
            $node->save();  
            }       
        }

        $output[] = array("fileId"=>$fileId,"familyId"=>$familyId);
        return new JsonResponse(array("output"=>$output));
    }
}
?>