<?php
namespace Drupal\general\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\general\Event\generalEvent;

class GeneralForm extends FormBase{
    public function getFormId(){
        return "general_custom_form";
    }

    public function buildForm(array $form, FormStateInterface $form_state){
    //     $form['name']= array(
    //         "type"=>"textfield",
    //         "title" => $this->t("Name"),
    //   '#maxlength' => 64,
    //   '#size' => 64,
    //     );

        $form['name'] = array(
            '#type' => 'textfield',
            '#title' => t('Employee Name:'),
            // '#required' => TRUE,
          );

          $form['actions']['#type'] = 'actions';
          $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Register'),
            '#button_type' => 'primary',
          );

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
  
            $name = $form_state->getValue('name');
            if($name == ""){
                $form_state->setErrorByName('name',$this->t('Please Enter Name'));
            }
        }


        
        public  function submitForm(array &$form,FormStateInterface $form_state){
            $name = $form_state->getValue('name');
            $dispatcher = \Drupal::service("event_dispatcher");
            
            $generalevent = new generalEvent(100);
            $dispatcher->dispatch(generalEvent::SUBMIT, $generalevent);
        }

    }



?>