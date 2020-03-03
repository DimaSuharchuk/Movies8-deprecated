<?php

namespace Drupal\imdb;

use Drupal;
use Drupal\Component\Serialization\Json;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Exception;
use GuzzleHttp\Exception\RequestException;

/**
 * Class TMDbFetcher Test.
 *
 * @package Drupal\imdb
 */
class TMDbFetcher {

  use StringTranslationTrait;

  const BASE_URL = 'https://api.themoviedb.org/3';

  /**
   * TMDB API key.
   *
   * @var array|null
   */
  private $tmdbApiKey;

  /**
   * Http client.
   *
   * @var \GuzzleHttp\Client
   */
  private $http;

  /**
   * Type of content should be fetched.
   *
   * @var string
   */
  private $type;

  /**
   * IMDB or TMDB movie ID, collection ID.
   *
   * @var string
   */
  private $id;

  /**
   * Language of content should be fetched.
   *
   * @var string
   */
  private $lang = 'en';

  /**
   * Array of additional query info.
   *
   * @var array
   */
  private $additionalQuery = [];


  public function __construct() {
    $this->http = Drupal::httpClient();
    $this->tmdbApiKey = Drupal::config('imdb.settings')->get('tmdb_api_key');
  }

  public function type($type) {
    $this->type = $type;
    return $this;
  }

  public function id($id) {
    $this->id = $id;
    return $this;
  }

  public function language($lang) {
    $this->lang = $lang;
    return $this;
  }
  public function languageAll() {
    $this->lang = 'all';
    return $this;
  }

  public function fullQuery() {
    $this->additionalQuery = [
      'append_to_response' => 'recommendations,videos,images,credits',
      'include_image_language' => "{$this->lang},null",
    ];
    return $this;
  }

  public function execute() {
    // Recursive collect TMDb data for all languages.
    if ($this->lang == 'all') {
      $results = [];

      foreach (['en', 'ru', 'uk'] as $lang) {
        $results[$lang] = (new self())
          ->type($this->type)
          ->id($this->id)
          ->language($lang)
          ->execute();
      }

      return $results;
    }

    // Define url options for all requests.
    $urlOptions = [
      'query' => [
        'api_key' => $this->tmdbApiKey,
        'language' => $this->lang,
      ],
    ];

    // Check data's type and append url options if need.
    switch ($this->type) {
      case 'movie':
        $urlOptions['query'] += $this->additionalQuery;
        break;

      default:
        throw new Exception($this->t('Undefined type %type', [
          '%type' => $this->type,
        ]));
    }

    // Create request url.
    $url = Url::fromUri(self::BASE_URL . '/' . $this->type . '/' . $this->id, $urlOptions)
      ->toString();

    // Request to TMDb.
    try {
      $response = $this->http->get($url);
      $data = $response->getBody()->getContents();

      return Json::decode($data);
    }
    catch (RequestException $e) {
      Drupal::messenger()->addError($e->getMessage());
    }

    return FALSE;
  }

}

/*
~~~Movie~~~
https://api.themoviedb.org/3/movie/109445?api_key=5fbddf6b517048e25bc3ac1bbeafb919&language=uk&append_to_response=recommendations,similar,videos,images,credits&include_image_language=uk,null
~~~Image~~~
https://image.tmdb.org/t/p/original/xJWPZIYOEFIjZpBL7SVBGnzRYXp.jpg
~~~Api key~~~
5fbddf6b517048e25bc3ac1bbeafb919
~~~TV~~~
https://api.themoviedb.org/3/tv/19885?api_key=5fbddf6b517048e25bc3ac1bbeafb919&language=uk&append_to_response=recommendations,similar,videos,images,credits,external_ids&include_image_language=uk,null
https://api.themoviedb.org/3/tv/1399?api_key=5fbddf6b517048e25bc3ac1bbeafb919&language=ru-RU&append_to_response=recommendations,similar,videos,credits,external_ids
*/
