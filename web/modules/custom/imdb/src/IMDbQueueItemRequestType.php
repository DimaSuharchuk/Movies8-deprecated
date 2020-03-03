<?php

namespace Drupal\imdb;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * @method static FIND()
 * @method static MOVIE()
 * @method static TV()
 */
final class IMDbQueueItemRequestType extends AbstractEnumeration {

  const FIND = 'find';

  const MOVIE = 'movie';

  const TV = 'tv';

}
