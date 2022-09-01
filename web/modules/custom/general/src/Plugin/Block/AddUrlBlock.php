<?php
/**
 * @file
 * Contains \Drupal\general\Plugin\Block\ArticleBlock.
 */

namespace Drupal\general\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;

/**
 * Provides a 'general' block.
 *
 * @Block(
 *   id = "addurl_block",
 *   admin_label = @Translation("Add Url block"),
 *   category = @Translation("Custom Add Url block example")
 * )
 */
class AddUrlBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $form = \Drupal::formBuilder()->getForm('Drupal\general\Form\AddUrlForm');

    return $form;
   }
}