<?php

namespace Drupal\resource;

use Drupal\Core\Entity\ContentEntityInterface;
use Iterator;

abstract class CollectionDrupalEntity implements CollectionDrupalEntityInterface, Iterator {

  /**
   * Encapsulated array used as collection for entities.
   *
   * @var array
   */
  protected $list = [];


  /**
   * {@inheritDoc}
   */
  public function __construct() {
  }

  /**
   * {@inheritDoc}
   */
  public function add(ContentEntityInterface $entity): CollectionDrupalEntity {
    $this->list[] = $entity;
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function current() {
    return current($this->list);
  }

  /**
   * {@inheritDoc}
   */
  public function next() {
    return next($this->list);
  }

  /**
   * {@inheritDoc}
   */
  public function valid() {
    $key = $this->key();
    return $key !== NULL && $key !== FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public function key() {
    return key($this->list);
  }

  /**
   * {@inheritDoc}
   */
  public function rewind() {
    reset($this->list);
  }

}
