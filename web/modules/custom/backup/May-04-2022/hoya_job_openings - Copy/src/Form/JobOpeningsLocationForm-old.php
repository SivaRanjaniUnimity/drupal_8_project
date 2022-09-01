<?php
/**
 * JobOpeningsLocationForm File Doc Comment
 * php version 7.4.26

 * @category Components
 * @package  Drupal
 * @author   Hoya <hso-ghq-hr@hoya.com>
 * @license  https://github.com/hoyasurgicaloptics/LICENSE.txt hoyasurgicaloptics  
 * @link     https://hoyasurgicalopticsdev.prod.acquia-sites.com/ *
 */

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
 * JobOpeningsLocationForm File Doc Comment
 * php version 7.4.26

 * @category Components
 * @package  Drupal
 * @author   Hoya <hso-ghq-hr@hoya.com>
 * @license  https://github.com/hoyasurgicaloptics/LICENSE.txt hoyasurgicaloptics  
 * @link     https://hoyasurgicalopticsdev.prod.acquia-sites.com/ *
 */
class JobOpeningsLocationForm extends FormBase
{

    /**
     * An instance of the entity type manager.  
     * 
     * @param EntityTypeManagerInterface $entityTypeManager
     */
    protected $entityTypeManager;

    /**
     * {@inheritdoc}
     * 
     * __construct Method.
     * 
     * @param \Drupal\Core\Entity\EntityInterface $entityTypeManager 
     *                                                               entity object.
     */
    public function __construct(EntityTypeManagerInterface $entityTypeManager)
    {
        $this->entityTypeManager = $entityTypeManager;
    }

    /**
     * {@inheritdoc}
     * 
     * @param ContainerInterface $container 
     *                                      ContainerInterface instance
     * 
     * @return EntityTypeManagerInterface $entityTypeManager 
     *  The response containing the entity.
     */
    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('entity_type.manager')
        );
    }

    /**
     * {@inheritdoc}
     * 
     * @return string 
     *   return formId 
     */
    public function getFormId()
    {
        return 'hoya_job_openings_location_filter';
    }

    /**
     * {@inheritdoc}
     * 
     * BuildForm Method.
     * 
     * @param array              $form       
     *                                       array
     *                                       instance
     * @param FormStateInterface $form_state 
     *                                       FormStateInterface instance
     * 
     * @return array 
     *  Return array instance
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $service = \Drupal::service('hoya_job_openings.services');    
        $fetch_location_list = $service->fetchTermdata('office_location');
        $fetch_role_list = $service->fetchTermdata('designation');

        $form['#attached']['library'][] = 'hoya_job_openings/hoya_job_openings';

        $form['filter_by'] = [
        '#type' => 'markup',
        '#prefix' => '<div class="row"><div class="col-md-4"><div class="d-flex">',
        '#markup' => '<div id="filter-by-cnt"><i class="fas fa-filter"></i>
        Filter By</div>'
        ];

        $form['job_location'] = [
        '#type' => 'checkboxes',
        '#options' => $fetch_location_list['options'],
        '#options_attributes' =>  $fetch_location_list['option_attribute'],
        '#title' => $this->t('Job Location'),
        '#ajax' => [
        'callback' => '::fetchlocationResult',
        'disable-refocus' => false, 
        // Or TRUE to prevent re-focusing on the triggering element.
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Processing...'),
        ],
        ]
        ];

        $form['jobrole'] = [
        '#type' => 'checkboxes',
        '#options' => $fetch_role_list['options'],
        '#options_attributes' =>  $fetch_role_list['option_attribute'],
        '#title' => $this->t('Job Role'),
        '#ajax' => [
        'callback' => '::fetchResult',
        'disable-refocus' => false, 
        // Or TRUE to prevent re-focusing on the triggering element.
        'event' => 'change',
        'wrapper' => 'job-opening-result', 
        // This element is updated with this AJAX callback.
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Processing...'),
        ],
        ]
        ];

        $form['options']['reset'] = array(
        '#type' => 'submit',
        '#suffix' => '</div>',
        '#value' => t('Reset'),
        '#submit' => array('::resetResult'),
        );

    
        foreach ($fetch_location_list['termid_arr'] as $key=>$value) {     
            if ($value == 0) {
                $form['job_location'][$key]['#disabled'] = true;
            }
        }
        foreach ($fetch_role_list['termid_arr'] as $key=>$value) {     
            if ($value == 0) {
                $form['jobrole'][$key]['#disabled'] = true;
            }
        }

        $fetch_all_result = $service->getAllResult();
        $form['jobresult'] = [
        '#type' => 'markup',
        '#markup' => '<div id="job-opening-result" class="job-opening-result">'
        .$fetch_all_result .'</div>',  
        '#suffix' => '</div>'
        ];

        $form['map_container'] = [
        '#type' => 'markup',
        '#prefix' => '<div class="col-md-8">',
        '#markup' => '<div id="map-loc-container"></div>',  
        '#suffix' => '</div></div>'
        ];
        return $form;
    }

    /**
     * {@inheritdoc}
     * 
     * FetchResult Method.
     * 
     * @param array              $form       
     *                                       array
     *                                       instance
     * @param FormStateInterface $form_state 
     *                                       FormStateInterface instance
     * 
     * @return AjaxResponse 
     *  Return AjaxResponse instance
     */
    public function fetchResult(array &$form, FormStateInterface $form_state)
    {
        $ajax_response = new AjaxResponse();
        $jobrole = $form_state->getValue('jobrole');   
        $job_location = $form_state->getValue('job_location');
        $ajax_response->addCommand(new HtmlCommand('#job-opening-result', ''));   
        $nidarr = array();  

        if ((count(array_unique($job_location)) === 1) && (count(array_unique($jobrole)) === 1)) {
            $results = $this->entityTypeManager->getStorage('node')->loadByProperties(['type' => 'job_openings']);
        } elseif (!empty($jobrole)) {     
            $info = ['type' => 'job_openings', 'field_role' => $jobrole];  
            $results = $this->entityTypeManager->getStorage('node')->loadByProperties($info);
        }
        if (!empty($results)) {
            $entity_type = 'node';
            $view_mode = 'teaser';

            foreach ($results as $node) {
                $output = render(\Drupal::entityTypeManager()->getViewBuilder($entity_type)->view($node, $view_mode));
                $ajax_response->addCommand(new \Drupal\Core\Ajax\AppendCommand('#job-opening-result', $output));
            }             
        }
       
        return $ajax_response;
    }

    /**
     * {@inheritdoc}
     * 
     * FetchlocationResult Method.
     * 
     * @param array              $form       
     *                                       array
     *                                       instance
     * @param FormStateInterface $form_state 
     *                                       FormStateInterface instance
     * 
     * @return AjaxResponse 
     *  Return AjaxResponse instance
     */
    public function fetchlocationResult(array &$form, FormStateInterface $form_state)
    {
        $ajax_response = new AjaxResponse();
        $job_location = $form_state->getValue('job_location');  
        $jobrole = $form_state->getValue('jobrole');   
        $ajax_response->addCommand(new \Drupal\Core\Ajax\HtmlCommand('#job-opening-result', ''));   
        $nidarr = array();   

        if ((count(array_unique($job_location)) === 1) && (count(array_unique($jobrole)) === 1)) {
            $results = $this->entityTypeManager->getStorage('node')->loadByProperties(['type' => 'job_openings']);
        } elseif (!empty($job_location)) {  
            $info=['type' => 'job_openings', 'field_country' => $job_location];    
            $results = $this->entityTypeManager->getStorage('node')->loadByProperties($info);
        }
        if (!empty($results)) {
            $entity_type = 'node';
            $view_mode = 'teaser';

            foreach ($results as $node) {
                $output = render(\Drupal::entityTypeManager()->getViewBuilder($entity_type)->view($node, $view_mode));
                $ajax_response->addCommand(new \Drupal\Core\Ajax\AppendCommand('#job-opening-result', $output));

                $available_roles = $node->get('field_role')->getString();  
                $form['jobrole'][$available_roles]['#disabled'] = true;
            }        
        }
          
        return $ajax_response;
    }

    /**
     * {@inheritdoc}
     * 
     * ResetResult Method.
     * 
     * @param array              $form       
     *                                       array
     *                                       instance
     * @param FormStateInterface $form_state 
     *                                       FormStateInterface instance
     * 
     * @return AjaxResponse 
     *  Return AjaxResponse instance
     */
    public function resetResult(array &$form, FormStateInterface $form_state)
    {
        $ajax_response = new AjaxResponse();
        $ajax_response->addCommand(new \Drupal\Core\Ajax\HtmlCommand('#job-opening-result', ''));   

        if (!empty($job_location)) {
            $nidarr = array();   
            $results = $this->entityTypeManager->getStorage('node')->loadByProperties(['type' => 'job_openings']);

            if (!empty($results)) {
                $entity_type = 'node';
                $view_mode = 'teaser';

                foreach ($results as $node) {
                    $render_info = \Drupal::entityTypeManager()->getViewBuilder($entity_type)->view($node, $view_mode);
                    $output = render($render_info);
                    $ajax_response->addCommand(new \Drupal\Core\Ajax\AppendCommand('#job-opening-result', $output));

                    $available_roles = $node->get('field_role')->getString();  
                    $form['jobrole'][$available_roles]['#disabled'] = true;
                }        
            }
        }    
        return $ajax_response;
    }

    /**
     * {@inheritdoc}
     * 
     * ResetResult Method.
     * 
     * @param array              $form       
     *                                       array
     *                                       instance
     * @param FormStateInterface $form_state 
     *                                       FormStateInterface instance
     * 
     * @return AjaxResponse 
     *  Return AjaxResponse instance
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
    }

    /**
     * {@inheritdoc}
     * 
     * ResetResult Method.
     * 
     * @param array              $form       
     *                                       array
     *                                       instance
     * @param FormStateInterface $form_state 
     *                                       FormStateInterface instance
     * 
     * @return AjaxResponse 
     *  Return AjaxResponse instance
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
    }
}
