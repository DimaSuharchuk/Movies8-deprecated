<?php

namespace Drupal\imdb;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * @method static ENG()
 * @method static RUS()
 * @method static UKR()
 */
final class IMDbQueueItemLanguage extends AbstractEnumeration {

  const ENG = 'en';

  const RUS = 'ru';

  const UKR = 'uk';

}
