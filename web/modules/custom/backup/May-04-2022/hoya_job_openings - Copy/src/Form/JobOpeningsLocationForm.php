<?php

namespace Drupal\hoya_job_openings\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Entity\EntityTypeManager;

/**
 * Provides a Hoya Job Openings form.
 */
class JobOpeningsLocationForm extends FormBase {

  /**
   * An instance of the entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'hoya_job_openings_location_filter';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $service = \Drupal::service('hoya_job_openings.services');    
    $fetch_location_list = $service->fetchTermdata('office_location');
    $fetch_role_list = $service->fetchTermdata('job_roles');

    $form['#attached']['library'][] = 'hoya_job_openings/hoya_job_openings';

    $form['close_link'] = [
      '#type' => 'markup',
      '#markup' => '<div class="job-openings-wrapper"><div class="close-icon-cnt"><a class="coh-link close-popup-link visible-white-co" href="/" target="_self"><img class="coh-image white-cross-icon coh-image-responsive-xl" src="/sites/default/files/close.png" title="Click to close" alt="Click to close"></a></div><h2 class="jobopen-h2-title">Job Openings</h2>'
    ];

    $form['filter_by'] = [
      '#type' => 'markup',
      '#prefix' => '<div class="row"><div class="col-md-4"><div class="d-flex">',
      '#markup' => '<div id="filter-by-cnt"><img class="filter-icon" src="themes/custom/hoyasurgicaloptics/assets/svg/ionic-ios-options.svg">Filter By</div>'
    ];

    

    $form['jobrole'] = [
      '#type' => 'checkboxes',      
      '#options' => $fetch_role_list['options'],
      '#options_attributes' =>  $fetch_role_list['option_attribute'],
      '#title' => $this->t('Role'),
      '#ajax' => [
        'callback' => '::fetchResult',
        'disable-refocus' => FALSE, // Or TRUE to prevent re-focusing on the triggering element.
        'event' => 'change',
        'wrapper' => 'job-opening-result', // This element is updated with this AJAX callback.
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Processing...'),
        ],
      ]
    ];

    $form['job_location'] = [
      '#type' => 'checkboxes',
      '#suffix' => '</div>',
      '#options' => $fetch_location_list['options'],
      '#options_attributes' =>  $fetch_location_list['option_attribute'],
      '#title' => $this->t('Location'),
      '#ajax' => [
        'callback' => '::fetchlocationResult',
        'disable-refocus' => FALSE, // Or TRUE to prevent re-focusing on the triggering element.
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Processing...'),
        ],
      ]
    ];

    $form['options']['reset'] = array(
      '#type' => 'submit',      
      '#value' => t('Clear filter'),
      '#submit' => array('::resetResult'),
    );

    
    foreach($fetch_location_list['termid_arr'] as $key=>$value){     
      if($value == 0){
        $form['job_location'][$key]['#disabled'] = TRUE;
      }
    }
    foreach($fetch_role_list['termid_arr'] as $key=>$value){     
      if($value == 0){
        $form['jobrole'][$key]['#disabled'] = TRUE;
      }
    }

    $fetch_all_result = $service->getAllResult();
    $form['jobresult'] = [
      '#type' => 'markup',
      '#markup' => '<div id="job-opening-result" class="job-opening-result"><div id="result-container">'.$fetch_all_result .'</div></div>',  
      '#suffix' => '</div>'
    ];

    $form['map_container'] = [
      '#type' => 'markup',
      '#prefix' => '<div class="col-md-8">',
      '#markup' => '<div id="map-loc-container"></div>',  
      '#suffix' => '</div></div></div>'
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function fetchResult(array &$form, FormStateInterface $form_state) {
    $ajax_response = new AjaxResponse();
    $jobrole = $form_state->getValue('jobrole');   
    $job_location = $form_state->getValue('job_location');
    $ajax_response->addCommand(new \Drupal\Core\Ajax\HtmlCommand('#result-container', '' ));   
    $nidarr = array();  

    if((count(array_unique($job_location)) === 1) && (count(array_unique($jobrole)) === 1)) {
      $results = $this->entityTypeManager->getStorage('node')->loadByProperties(['type' => 'job_openings']);
    }
    elseif (!empty($jobrole)) {       
      $results = $this->entityTypeManager->getStorage('node')->loadByProperties(['type' => 'job_openings', 'field_role' => $jobrole]);
    }
    if (!empty($results))
    {
      $entity_type = 'node';
      $view_mode = 'teaser';

      foreach($results as $node){
          $output = render(\Drupal::entityTypeManager()->getViewBuilder($entity_type)->view($node, $view_mode));
          $ajax_response->addCommand(new \Drupal\Core\Ajax\AppendCommand('#result-container', $output ));
      }             
    }
       
    return $ajax_response;
  }


  /**
   * {@inheritdoc}
   */
  public function fetchlocationResult(array &$form, FormStateInterface $form_state) {
    $ajax_response = new AjaxResponse();
    $job_location = $form_state->getValue('job_location');  
    $jobrole = $form_state->getValue('jobrole');   
    $ajax_response->addCommand(new \Drupal\Core\Ajax\HtmlCommand('#result-container', '' ));   
    $nidarr = array();   

    if((count(array_unique($job_location)) === 1) && (count(array_unique($jobrole)) === 1)) {
      $results = $this->entityTypeManager->getStorage('node')->loadByProperties(['type' => 'job_openings']);
    }
    elseif (!empty($job_location)) {      
      $results = $this->entityTypeManager->getStorage('node')->loadByProperties(['type' => 'job_openings', 'field_country' => $job_location]);
    }
    if (!empty($results))
    {
      $entity_type = 'node';
      $view_mode = 'teaser';

      foreach($results as $node){
          $output = render(\Drupal::entityTypeManager()->getViewBuilder($entity_type)->view($node, $view_mode));
          $ajax_response->addCommand(new \Drupal\Core\Ajax\AppendCommand('#result-container', $output ));

          $available_roles = $node->get('field_role')->getString();  
          $form['jobrole'][$available_roles]['#disabled'] = TRUE;
      }        
    }
          
    return $ajax_response;
  }

  /**
   * {@inheritdoc}
   */
  public function resetResult(array &$form, FormStateInterface $form_state) {
    $ajax_response = new AjaxResponse();
    $ajax_response->addCommand(new \Drupal\Core\Ajax\HtmlCommand('#result-container', '' ));   

    if (!empty($job_location)) {
      $nidarr = array();   
      $results = $this->entityTypeManager->getStorage('node')->loadByProperties(['type' => 'job_openings']);

      if (!empty($results))
      {
        $entity_type = 'node';
        $view_mode = 'teaser';

        foreach($results as $node){
            $output = render(\Drupal::entityTypeManager()->getViewBuilder($entity_type)->view($node, $view_mode));
            $ajax_response->addCommand(new \Drupal\Core\Ajax\AppendCommand('#result-container', $output ));

            $available_roles = $node->get('field_role')->getString();  
            $form['jobrole'][$available_roles]['#disabled'] = TRUE;
        }        
      }
      }    
    return $ajax_response;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
}
}
