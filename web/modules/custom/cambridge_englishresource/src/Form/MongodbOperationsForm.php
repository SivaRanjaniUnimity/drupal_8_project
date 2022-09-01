<?php

namespace Drupal\cambridge_englishresource\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\mongodb\MongoDb;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
* Class MongodbOperationsForm.
*/
class MongodbOperationsForm extends FormBase {

/**
* Drupal\mongodb\DatabaseFactory definition.
*
* @var \Drupal\mongodb\DatabaseFactory
*/
protected $mongodbDatabaseFactory;
protected $messenger;
protected $current_request;

/**
* {@inheritdoc}
*/
public static function create(ContainerInterface $container) {
$instance = parent::create($container);
$instance->mongodbDatabaseFactory = $container->get('mongodb.database_factory');
$instance->messenger = $container->get('messenger');
$instance->current_request = $container->get('request_stack');
return $instance;
}

/**
* {@inheritdoc}
*/
public function getFormId() {
return 'mongodb_operations_form';
}

/**
* {@inheritdoc}
*/
public function buildForm(array $form, FormStateInterface $form_state) {
$id = $this->current_request->getCurrentRequest()->get("id");
if (!empty($id)) {
// $this->getRowsById($id);
}
$form['first_name'] = [
'#type' => 'textfield',
'#title' => $this->t('First Name'),
'#maxlength' => 64,
'#size' => 64,
'#weight' => '0',
];
$form['last_name'] = [
'#type' => 'textfield',
'#title' => $this->t('Last Name'),
'#maxlength' => 64,
'#size' => 64,
'#weight' => '0',
];
$form['submit'] = [
'#type' => 'submit',
'#value' => $this->t('Save'),
'#weight' => '0',
];

// $form['rows'] = [
// '#theme' => 'table',
// '#header' => ['id', 'frist name', 'last name', 'link'],
// '#rows' => $this->getRowsFromMongo(),
// '#empty' =>t('Your table is empty'),
// ];
$form['#cache'] = ['max-age' => 0];
return $form;
}

/**
* {@inheritdoc}
*/
public function validateForm(array &$form, FormStateInterface $form_state) {
if (!empty($form_state->getValues()['first_name'])) {
$database = $this->mongodbDatabaseFactory->get("collection");
$collection = $database->selectCollection('isbn_attributes');
$rows = $collection->find(['first_name' => $form_state->getValues()['first_name']]);
if (count($rows->toArray())) {
$form_state->setErrorByName("first_name", "Fname Already exists");
}
}
parent::validateForm($form, $form_state);
}

/**
* {@inheritdoc}
*/
public function submitForm(array &$form, FormStateInterface $form_state) {
if (!empty($id)) {
//@todo update record with ID match.
} else {
$database = $this->mongodbDatabaseFactory->get("logger");
$collection = $database->selectCollection('mongodb_operations_form');
$collection->insertOne([
"first_name" => $form_state->getValue("first_name"),
"last_name" => $form_state->getValue("last_name"),
]);
$this->messenger->addMessage("Mongo insertion done!!");
}

}

protected function getRowsById($id = "") {
$database = $this->mongodbDatabaseFactory->get("logger");
$collection = $database->selectCollection('mongodb_operations_form');
$rows = $collection->findOne(["_id" => new \MongoDB\BSON\ObjectID($id)]);
dump($rows->toArray());
die;
}


public function getRowsFromMongo() {
$database = $this->mongodbDatabaseFactory->get("logger");
$collection = $database->selectCollection('mongodb_operations_form');
$rows = $collection->find();
$tableRows = [];
foreach( $rows as $row) {
$url = Url::fromRoute('cambridge_englishresource.mongodb_operations_form', array('id' => (string)$row->_id));
$project_link = Link::fromTextAndUrl(t('Edit'), $url);

$tableRows[] = [
'id' => (string)$row->_id,
'fname' => $row->first_name,
'lname' => $row->last_name,
'link' => $project_link->toString(),
];
}
return $tableRows;
}

}