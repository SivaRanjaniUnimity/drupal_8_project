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
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManager $entity_type_manager) {
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

    $form['job_location'] = [
      '#type' => 'checkboxes',
      '#options' => $fetch_location_list,
      '#title' => $this->t('Job Location'),
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
    // dpm($fetch_role_list);

    $form['jobrole'] = [
      '#type' => 'checkboxes',
      '#options' => $fetch_role_list,
      '#title' => $this->t('Job Role'),
      '#ajax' => [
        'callback' => '::fetchResult',
        'disable-refocus' => FALSE, // Or TRUE to prevent re-focusing on the triggering element.
        'event' => 'change',
        'wrapper' => 'job-opening-result', // This element is updated with this AJAX callback.
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Verifying entry...'),
        ],
      ]
    ];

    $form['jobresult'] = [
      '#type' => 'markup',
      '#markup' => '<div id="job-opening-result" class="job-opening-result"></div>',  
  ];

   $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
    ];

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
      foreach($jobrole as $tid){
       
      $nidarr = array();   
      if (!empty($tid)) {
         
          // $query = \Drupal::entityQuery('node')->condition('type', 'job_openings')
          //         ->condition('field_role', $tid, '=');        
          // $results = $query->execute();

          // $query = $this->entityQuery->get('node');
          // $query->condition('field_role', $tid, '=');
          // $results = $query->execute();

          // $results = $this->entityTypeManager->getStorage('node')->loadByProperties(['type' => 'job_openings']);

          $results = $this->entityTypeManager->getStorage('node')->loadByProperties(['type' => 'job_openings', 'field_role' => 10]);

          if (!empty($results))
          {
              foreach($results as $node){
                  $nidarr[] = $node->id();
              }                            
              $args = array(implode("+",$nidarr));
              $view = \Drupal\views\Views::getView('job_openings');
              if (is_object($view)) {
                  $view->setArguments($args);
                  $view->setDisplay('block_1');
                  $view->preExecute();
                  $view->execute();

                  $rendered = $view->render();
                  $output = \Drupal::service('renderer')->render($rendered);
                  $ajax_response->addCommand(new \Drupal\Core\Ajax\AppendCommand('#job-opening-result', $output ));
              }                
          }
      }
      }
         
      }    
    return $ajax_response;
  }


  /**
   * {@inheritdoc}
   */
  public function fetchlocationResult(array &$form, FormStateInterface $form_state) {
    $ajax_response = new AjaxResponse();
    $jobrole = $form_state->getValue('job_location');   
    
    $ajax_response->addCommand(new \Drupal\Core\Ajax\HtmlCommand('#job-opening-result', '' ));

    if (!empty($jobrole)) {   
      foreach($jobrole as $tid){
       
      $nidarr = array();   
      if (!empty($tid)) {
         
          $query = \Drupal::entityQuery('node')->condition('type', 'job_openings')
                  ->condition('field_country', $tid, '=');
        
          $results = $query->execute();
          
          
          if (!empty($results))
          {
              foreach($results as $nid){
                  $nidarr[] = $nid;
              }                            
              $args = array(implode("+",$nidarr));
              $view = \Drupal\views\Views::getView('job_openings');
              if (is_object($view)) {
                  $view->setArguments($args);
                  $view->setDisplay('block_1');
                  $view->preExecute();
                  $view->execute();

                  $rendered = $view->render();
                  $output = \Drupal::service('renderer')->render($rendered);                
                  $ajax_response->addCommand(new \Drupal\Core\Ajax\AppendCommand('#job-opening-result', $output ));
    
              }                
          }
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
