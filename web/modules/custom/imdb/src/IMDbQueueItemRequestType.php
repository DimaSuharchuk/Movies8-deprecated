<?php

namespace Drupal\imdb;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * @method static FIND()
 * @method static MOVIE()
 * @method static TV()
 * @method static SEASON()
 * @method static EPISODE()
 * @method static EPISODE_EXTERNAL_IDS()
 */
final class IMDbQueueItemRequestType extends AbstractEnumeration {

  const FIND = 'find';

  const MOVIE = 'movie';

  const TV = 'tv';

  const SEASON = 'season';

  const EPISODE = 'episode';

  const EPISODE_EXTERNAL_IDS = 'episode_external_ids';

}
