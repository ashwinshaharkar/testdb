<?php

namespace Drupal\protected_file\Tests;

use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\file\FileInterface;
use Drupal\file\Entity\File;

/**
 * Provides testing for Protected File module's field handling.
 *
 * @group protected_file
 */
class ProtectedFileTest extends ProtectedFileTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('node', 'file', 'protected_file', 'field_ui');

  /**
   * An user with administration permissions.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
  }

  /**
   * Provide tests for a protected file type.
   */
  public function testProtectedFile() {
    $this->drupalGet('admin/config/media/file-system');
    $fields = array(
      'file_default_scheme' => 'private',
    );
    // Check that public and private can be selected as default scheme.
    $this->assertText('Public local files served by the webserver.');
    $this->assertText('Private local files served by Drupal.');
    $this->drupalPostForm(NULL, $fields, t('Save configuration'));
    $this->assertText(t('The configuration options have been saved.'));

    $type_name = 'article';
    $field_name = strtolower($this->randomMachineName());
    $storage_settings = [
      'cardinality' => -1,
      'display_field' => TRUE,
      'display_default' => TRUE,
      'uri_scheme' => 'private',
    ];
    $field_settings = array(
      'description_field' => '1',
      'file_directory' => '',
    );
    $this->createProtectedFileField($field_name, 'node', $type_name, $storage_settings, $field_settings);
    $field = FieldConfig::loadByName('node', $type_name, $field_name);
    $field_id = $field->id();

    $this->drupalGet("admin/structure/types/manage/$type_name/fields/$field_id/storage");
    $this->assertFieldByXpath('//input[@id="edit-settings-uri-scheme-public" and @disabled="disabled"]', 'public', 'Upload destination setting disabled.');

    $this->drupalGet("admin/structure/types/manage/$type_name/display");
    $this->assertFieldByName("fields[$field_name][type]", 'protected_file_formatter', 'The expected formatter is selected.');
    $this->drupalGet("admin/structure/types/manage/$type_name/form-display");
    $this->assertFieldByName("fields[$field_name][type]", 'protected_file_widget', 'The expected widget is selected.');
    $this->drupalGet("admin/structure/types/manage/$type_name/fields/$field_id");
    $this->assertFieldChecked('edit-settings-description-field');

    $contents = $this->randomMachineName(8);
    $contents_other = $this->randomMachineName(8);
    $file = $this->createFile('file1.txt', $contents, 'private');
    $file->setPermanent();
    $file->save();

    $file_other = $this->createFile('file2.txt', $contents_other, 'private');
    $file_other->setPermanent();
    $file_other->save();

    $files = [$file, $file_other];
    $nid = $this->uploadNodeFiles($files, $field_name, $type_name);
    $this->drupalGet("/node/$nid");
    $this->assertText($file->getFilename());
    $this->assertText($file_other->getFilename());

    // Add a description and make sure that it is displayed.
    // Protect the first file.
    $this->drupalGet("/node/$nid/edit");
    $description = 'file description';
    $description_other = 'file other description';
    $edit = array(
      $field_name . '[0][description]' => $description,
      $field_name . '[0][display]' => TRUE,
      $field_name . '[0][protected_file]' => TRUE,
      $field_name . '[1][description]' => $description_other,
      $field_name . '[1][display]' => TRUE,
    );
    $this->drupalPostForm('node/' . $nid . '/edit', $edit, t('Save and keep published'));
    $this->assertText($description);
    $this->assertNoText($file->getFilename());
    $this->assertText($description_other);

    $this->drupalGet("/node/$nid");
    $url_file = $this->getUrlLink($description);
    $this->clickLink($description);
    $this->assertResponse(200, 'Correctly allowed access to the file');

    $this->drupalGet("/node/$nid");
    $url_file_other = $this->getUrlLink($description_other);
    $this->clickLink($description_other);
    $this->assertResponse(200, 'Correctly allowed access to the file');

    // Anonymous can not access to file protected.
    $this->drupalLogout();
    $this->drupalGet("/node/$nid");
    $this->assertLinkByHref('/user/login', 0, 'Url to protected file has been replaced');
    $this->clickLink($description);
    $this->assertText('Log in');
    $this->assertResponse(200, 'Anonymous get user login page');

    $this->drupalGet("/node/$nid");
    // Try to download the file directly.
    $this->drupalGet($url_file);
    $this->assertResponse(403, 'Anonymous can not download protected file');

    $this->drupalGet("/node/$nid");
    $this->clickLink($description_other);
    $this->assertResponse(200, 'Correctly allowed access to the file');
    // Try to download the file directly.
    $this->drupalGet($url_file_other);
    $this->assertResponse(200, 'Anonymous can download file not protected');

  }

}
