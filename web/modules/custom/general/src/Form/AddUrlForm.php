<?php
namespace Drupal\general\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\general\Event\generalEvent;
use Drupal\node\Entity\Node;

class AddUrlForm extends FormBase{
    public function getFormId(){
        return "general_addurl_form";
    }

    public function buildForm(array $form, FormStateInterface $form_state){
        $form['help'] = [
            '#type' => 'item',
            '#title' => t('Add a URL'),
            '#prefix' => '<div class="tab-links tab-container">',
            // '#markup' => t('Block content'),
          ];

        $form['add_url'] = array(
            '#type' => 'textfield',
            '#title' => t('Url'),
            // '#required' => TRUE,
          );
        
        $form['link_text'] = array(
        '#type' => 'textfield',
        '#title' => t('Link text
        '),
        // '#required' => TRUE,
        );

        $query = \Drupal::entityQuery('taxonomy_term');
        $query->condition('vid', "audience");
        $tids = $query->execute();
        $terms = \Drupal\taxonomy\Entity\Term::loadMultiple($tids);
        $audience = array();
        foreach ($terms as $term) {
           $audience[$term->tid->value] = $term->name->value;
        }

        $form['audience'] = array (
            '#type' => 'select',
            '#title' => ('Audience'),
            // '#options' => array(
            //   'Male' => t('Male'),
            //   'Female' => t('Female'),
            //   'Other' => t('Other'),
            // ),
            '#options' => $audience
          );

          $form['include_outprint'] = array(
            '#type' => 'checkbox',
            '#title' => t('Include Out of Print Components'),
          );

          $form['new_window'] = array(
            '#type' => 'checkbox',
            '#title' => t('Open in new window'),
          );

          

          $form['actions']['#type'] = 'actions';
          $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Add URL'),
            '#button_type' => 'primary',
            '#suffix' => '</div>'
          );

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
  
            // $name = $form_state->getValue('name');
            // if($name == ""){
            //     $form_state->setErrorByName('name',$this->t('Please Enter Name'));
            // }
        }


        
        public  function submitForm(array &$form,FormStateInterface $form_state){
            $add_url = $form_state->getValue('add_url');
            $link_text = $form_state->getValue('link_text');
            $audience = $form_state->getValue('audience');
            $new_window = $form_state->getValue('new_window');
            $include_outprint = $form_state->getValue('include_outprint');
            $current_path = \Drupal::service('path.current')->getPath();      
            $split_url = explode("/",$current_path);
            $filterValueToSet ="";
            if(!empty($split_url)){
                $family_nid = end($split_url);
                $node = Node::load($family_nid);
                $strNodeTitle = $node->title->value;
            }
          
            // Create node object with attached file.
            $node = Node::create([
                'type'        => 'file_resource',
                'title'       => '-',
                'field_link_text' => $link_text,
                'field_family_relationship' => [$family_nid],
                'field_add_url' => $add_url,
                'field_audience' => [$audience],
                'field_include_outprint' => [$include_outprint],
                'field_open_in_new_window' => [$new_window],
            ]);
            $node->save();
           
            // $dispatcher = \Drupal::service("event_dispatcher");
            
            // $generalevent = new generalEvent(100);
            // $dispatcher->dispatch(generalEvent::SUBMIT, $generalevent);
        }

    }



?>