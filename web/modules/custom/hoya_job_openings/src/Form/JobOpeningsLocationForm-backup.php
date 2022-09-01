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
use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\Checkboxes;
use Drupal\Core\Render\Element\Radios;

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
    $fetch_role_list = $service->fetchTermdata('designation');

    $form['#attached']['library'][] = 'hoya_job_openings/hoya_job_openings';

    $form['job_location'] = [
      '#type' => 'checkboxes',
      '#options' => $fetch_location_list['options'],
      '#options_attributes' =>  $fetch_location_list['option_attribute'],
      '#title' => $this->t('Job Location'),
      '#ajax' => [
        'callback' => '::fetchlocationResult',
        // 'callback' => array($this, 'fetchlocationResult',$this, 'loadRoleList'),
     
        // 'callback' => [$this->fetchlocationResult,$this->loadRoleList],
        'disable-refocus' => FALSE, // Or TRUE to prevent re-focusing on the triggering element.
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Processing...'),
        ],
        'wrapper' => 'job-location-wrapper',
      ]
    ];
    
    $form['jobrole'] = [
      '#title' => $this->t('Job Role'),
      '#type' => 'checkboxes', 
      '#prefix' => '<div id="job-role-content">',
      '#suffix' => '</div>',     
      // for some reason you  need to set '#validated' => 'true' other wise tou get :
      // An illegal choice has been detected. Please contact the site administrator.
      '#validated' => 'true',
      // '#options' => $fetch_role_list['options'],
      '#options' => [],
      '#ajax' => [
        'callback' => '::fetchResult',
        'disable-refocus' => FALSE,
        'event' => 'change',
        'wrapper' => 'edit-jobrole-output',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Processing...'),
        ],
      ],      
    ];

    
    // $form['example_select2'] = [
      
    //   '#title' => $this->t('Select element'),
    //   '#type' => 'checkboxes',
    //   '#prefix' => '<div id="first">',
    //   '#suffix' => '</div>',
    //   // for some reason you  need to set '#validated' => 'true' other wise tou get :
    //   // An illegal choice has been detected. Please contact the site administrator.
    //   '#validated' => 'true',
    //   '#options' => [],
    //   '#ajax' => [
    //     'callback' => '::myAjaxCallback2',
    //     'disable-refocus' => FALSE,
    //     'event' => 'change',
    //     'wrapper' => 'edit-output',
    //     'progress' => [
    //       'type' => 'throbber',
    //       'message' => $this->t('Verifying entry...'),
    //     ],
    //   ],
    // ];

    
    foreach($fetch_location_list['termid_arr'] as $key=>$value){     
      if($value == 0){
        // $form['job_location'][$key]['#disabled'] = TRUE;
      }
    }
    foreach($fetch_role_list['termid_arr'] as $key=>$value){     
      if($value == 0){
        // $form['jobrole'][$key]['#disabled'] = TRUE;
      }
    }

    $fetch_all_result = $service->getAllResult('office_location');
    // dpm($fetch_all_result);
    $form['jobresult'] = [
      '#type' => 'markup',
      '#markup' => '<div id="job-opening-result" class="job-opening-result">'.$fetch_all_result.'</div>',  
    ];
    $form['example_select2'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Select element'),
      // The "wrapper" id that the ajax response will be injected into
      // must have an id ="wrapper set on select 1 form'.
      '#prefix' => '<div id="first">',
      '#suffix' => '</div>',
      // for some reason you  need to set '#validated' => 'true' other wise tou get :
      // An illegal choice has been detected. Please contact the site administrator.
      '#validated' => 'true',
      '#options' => [],
      '#ajax' => [
        'callback' => '::myAjaxCallback',
        'disable-refocus' => FALSE,
        'event' => 'change',
        'wrapper' => 'edit-output',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Verifying entry...'),
        ],
      ],
    ];
   
    $form['map_container'] = [
      '#type' => 'markup',
      '#markup' => '<div id="map-loc-container"></div>',  
    ];


    $form['#cache'] = ['max-age' => 0];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function fetchResult(array &$form, FormStateInterface $form_state) {
    $ajax_response = new AjaxResponse();
    $jobrole = $form_state->getValue('jobrole');   

    $ajax_response->addCommand(new \Drupal\Core\Ajax\HtmlCommand('#job-opening-result', '' ));   

    if (!empty($jobrole)) {
      $nidarr = array();   
      $results = $this->entityTypeManager->getStorage('node')->loadByProperties(['type' => 'job_openings', 'field_role' => $jobrole]);

      if (!empty($results))
      {
        $entity_type = 'node';
        $view_mode = 'full';

        foreach($results as $node){
            $output = render(\Drupal::entityTypeManager()->getViewBuilder($entity_type)->view($node, $view_mode));
            $ajax_response->addCommand(new \Drupal\Core\Ajax\AppendCommand('#job-opening-result', $output ));
        }             
      }
      }    
    return $ajax_response;
  }

  public function myAjaxCallback(array &$form, FormStateInterface $form_state) {
    // 1.construct an array of choices
    // make the key = your select '#options' key
    // and value = options values that you want to use in example_select2.
    $arr = [
      '1' => ['first','uno','first one','Best'],
      '2' => ['Second','2nd place','not first'],
      '3' => ['Third','Three',''],
      '4' => ['Fourth','four','las one'],
    ];

    // 2.Get the selected option value
    // ex : 3
    // $selctedOption = $form_state->getValue("example_select");

    // 3.Choose an array based on the selected option value.
    // ex : $arr['3']
    $form['wrapper']['example_select2']['#options'] = $arr[1];
    Checkboxes::processCheckboxes( $form['wrapper']['example_select2'],$form_state,$form);
    
    // // return $form['example_select2'];
    $form_state->setRebuild(TRUE);
    return  $form['wrapper']['example_select2'];
  }
  /**
   * {@inheritdoc}
   */
  public function fetchlocationResult(array &$form, FormStateInterface $form_state) {
    $ajax_response = new AjaxResponse();
    $job_location = $form_state->getValue('job_location');   
    $ajax_response->addCommand(new \Drupal\Core\Ajax\HtmlCommand('#job-opening-result', '' ));   
    $fetch_role_list = array();
    if (!empty($job_location)) {
      $nidarr = array();   
      $results = $this->entityTypeManager->getStorage('node')->loadByProperties(['type' => 'job_openings', 'field_country' => $job_location]);
      $available_roles = "";
      if (!empty($results))
      {
        $entity_type = 'node';
        $view_mode = 'full';
        $arr = array();

        foreach($results as $node){
            $output = render(\Drupal::entityTypeManager()->getViewBuilder($entity_type)->view($node, $view_mode));
            $ajax_response->addCommand(new \Drupal\Core\Ajax\AppendCommand('#job-opening-result', $output ));         
            $available_roles = $node->get('field_role')->getString();  
            $termname = \Drupal\taxonomy\Entity\Term::load($available_roles)->get('name')->value;
            $arr[$available_roles]=$termname;
        }    
    
    // unset($form['jobrole']['#options'][9]);
    
        $form['jobrole']['#options'] = $arr;
        Checkboxes::processCheckboxes($form['jobrole'],$form_state,$form);
        $form_state->setRebuild(TRUE);
        $ajax_response->addCommand(new ReplaceCommand("#first", ($form['jobrole'])));
        
      }
      }
      else{
        $arr = [
          '11' => ['11']
        ];
    
        $form['jobrole']['#options'] = [];
        $form['jobrole']['#options'] = $arr;
        Checkboxes::processCheckboxes($form['jobrole'],$form_state,$form);
    
        // return $form['example_select2'];
        $form_state->setRebuild(TRUE);
        $ajax_response->addCommand(new ReplaceCommand("#job-role-content", ($form['jobrole'])));
       
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
