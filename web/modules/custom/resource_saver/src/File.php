<?php

namespace Drupal\resource_saver;

use Drupal;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\file\FileInterface;

abstract class File extends ResourceBase {

  protected $type = 'file';

  protected $bundle = 'file';

  /**
   * Path to file that should be downloaded and saved on site.
   *
   * @var string
   */
  protected $file_path;

  /**
   * Directory the file should be placed on site.
   *
   * @var string
   */
  protected $directory;

  /**
   * File constructor.
   *
   * @param string $file_path
   *   Path to file that should be downloaded and saved on site.
   * @param string|NULL $directory
   *   Directory the file should be placed on site.
   *
   * {@inheritDoc}
   */
  public function __construct(string $file_path, string $directory = NULL) {
    parent::__construct();

    $this->file_path = $file_path;
    $this->directory = $directory ? "public://{$directory}/" : 'public://';
  }

  /**
   * {@inheritDoc}
   */
  public function save(): ContentEntityInterface {
    /** @var FileSystemInterface $file_system */
    $file_system = Drupal::service('file_system');

    // Download file.
    $file_data = file_get_contents($this->file_path);
    // Prepare directory.
    $file_system->prepareDirectory($this->directory, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);
    // Save file.
    $image_name = $file_system->basename($this->file_path);
    $filepath = $file_system->saveData($file_data, "{$this->directory}{$image_name}", FileSystemInterface::EXISTS_REPLACE);
    // Register file in Drupal.
    /** @var FileInterface $file */
    $file = $this->newEntity();
    $file->setFileUri($filepath);
    $file->setFilename($image_name);
    $file->setMimeType(Drupal::service('file.mime_type.guesser')
      ->guess($this->file_path));
    $file->save();

    return $file;
  }

  /**
   * @param string $directory
   *
   * @return \Drupal\resource_saver\File
   */
  public function setDirectory(string $directory): self {
    $this->directory = $directory ? "public://{$directory}/" : 'public://';
    return $this;
  }

}
