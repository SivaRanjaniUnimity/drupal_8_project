<?php
namespace Drupal\general\EventSubscriber;

use Drupal\Core\Config\ConfigCrudEvent;
use Drupal\Core\Config\ConfigEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\general\Event\generalEvent;

class customsubscriber implements EventSubscriberInterface{
    public static function getSubscribedEvents(){
        $events[generalEvent::SUBMIT] = array('message_display',800);
        return $events;

    }
    public function message_display(){
        dpm("You have saved a configuration of submit");

    }

} 

?>