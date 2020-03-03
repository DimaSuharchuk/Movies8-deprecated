<?php

namespace Drupal\imdb\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Class IMDbQueue.
 *
 * @Annotation
 */
class IMDbQueue extends Plugin {

  public $id;

  public $limits = [];

}
