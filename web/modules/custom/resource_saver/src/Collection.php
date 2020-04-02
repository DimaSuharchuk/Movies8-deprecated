<?php

namespace Drupal\resource_saver;

use Iterator;

abstract class Collection implements CollectionInterface, Iterator {

  /**
   * Encapsulated array used as collection for resources.
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
  public function add(ResourceInterface $resource): Collection {
    $this->list[] = $resource;
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
