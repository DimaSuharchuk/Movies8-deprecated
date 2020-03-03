<?php

namespace Drupal\imdb;

/**
 * Class IMDbHelper.
 *
 * @package Drupal\imdb
 */
class IMDbHelper {

  /**
   * Check is the string a correct IMDb ID.
   *
   * @param string $id
   *   String for check.
   *
   * @return bool
   *   String is IMDb ID.
   */
  public function isImdbId($id) {
    return (bool) preg_match('/^tt\d{7}$/', $id);
  }

}
