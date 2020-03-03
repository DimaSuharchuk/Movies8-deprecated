<?php

namespace Drupal\imdb;

class IMDbQueueItem {

  /**
   * Type of object, like "movie", "tv", "find".
   *
   * @var IMDbQueueItemRequestType
   */
  private $requestType;

  /**
   * IMDb or TMDb ID.
   *
   * @var string
   */
  private $id;

  /**
   * Item language. "en", "ru" and "uk".
   *
   * @var IMDbQueueItemLanguage
   */
  private $lang;

  private $approvedStatus = FALSE;

  private $fieldsData = [];

  public function __construct(IMDbQueueItemRequestType $requestType, string $id, IMDbQueueItemLanguage $lang) {
    $this->requestType = $requestType;
    $this->id = $id;
    $this->lang = $lang;
  }

  public function setApprovedStatus(bool $approvedStatus): void {
    $this->approvedStatus = $approvedStatus;
  }

  /**
   * @param array $fieldsData
   */
  public function setFieldsData(array $fieldsData): void {
    $this->fieldsData = $fieldsData;
  }

  /**
   * @return string
   */
  public function getRequestType(): string {
    return $this->requestType->value();
  }

  public function getRequestTypeObject(): IMDbQueueItemRequestType {
    return $this->requestType;
  }

  /**
   * @return string
   */
  public function getId(): string {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getLang(): string {
    return $this->lang->value();
  }

  public function getLangObject(): IMDbQueueItemLanguage {
    return $this->lang;
  }

  /**
   * @return bool
   */
  public function getApprovedStatus(): bool {
    return $this->approvedStatus;
  }

  /**
   * @return array
   */
  public function getFieldsData(): array {
    return $this->fieldsData;
  }

}
