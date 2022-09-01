<?php

namespace Drupal\Tests\fallback_formatter\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

/**
 * Test basic functionality of the fallback formatter.
 *
 * @group fallback_formatter
 */
class FallbackFormatterOutputTest extends WebDriverTestBase {

  public static $modules = [
    'views',
    'views_ui',
    'field_ui',
    'node',
    'fallback_formatter',
    'fallback_formatter_test',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * The admin user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->drupalCreateContentType(['type' => 'article', 'name' => 'Article']);

    $this->adminUser = $this->drupalCreateUser([
      'administer views',
      'create article content',
      'access content overview',
      'administer node display',
    ]);
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Tests fallback formatter settings in a view.
   */
  public function testFormatterSettingsInView() {
    // Create new article.
    $edit = [
      'title[0][value]' => $this->randomMachineName(),
    ];
    $this->drupalPostForm('node/add/article', $edit, t('Save'));

    // Add a new field in content overview with the author.
    $this->drupalGet('admin/structure/views/nojs/add-handler/content/page_1/field');
    $edit = [
      'name[node_field_revision.uid]' => TRUE,
    ];
    $this->drupalPostForm(NULL, $edit, t('Add and configure fields'));

    // Set fallback formatter as the formatter for the new field.
    $edit = [
      'options[type]' => 'fallback',
    ];
    $this->drupalPostForm(NULL, $edit, t('Apply'));
    $this->drupalGet('admin/structure/views/nojs/handler/content/page_1/field/uid');
    $edit = [
      'fallback_formatter_settings[formatters][entity_reference_label][status]' => TRUE,
    ];
    $this->drupalPostForm(NULL, $edit, t('Apply'));
    $this->drupalGet('admin/structure/views/nojs/handler/content/page_1/field/uid');
    $this->assertFieldChecked('edit-fallback-formatter-settings-formatters-entity-reference-label-status', 'The correct field is checked.');
    $this->assertNoFieldChecked('edit-fallback-formatter-settings-formatters-author-status');
    $this->drupalPostForm(NULL, $edit, t('Apply'));
    $this->drupalPostForm(NULL, [], t('Save'));

    // Check the output of the formatter.
    $this->drupalGet('admin/content');
    $this->assertSession()->pageTextContains($this->adminUser->getDisplayName());
  }

  /**
   * Tests fallback formatter settings in managed display.
   */
  public function testFormatterSettingsInManageDisplay() {
    $page = $this->getSession()->getPage();
    $assert_session = $this->assertSession();

    $this->drupalGet('admin/structure/types/manage/article/display');
    $this->drupalPostForm(NULL, ['fields[body][type]' => 'fallback'], t('Save'));
    $page->pressButton('body_settings_edit');
    $checkbox = $assert_session->waitForField('fallback_formatter_settings[formatters][text_trimmed][status]');
    $this->assertNotEmpty($checkbox);
    $checkbox->check();
    $page->pressButton('Update');
    $assert_session->pageTextContains('Trimmed');
  }

}
