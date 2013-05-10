<?php


namespace Components;


  /**
   * I18n
   *
   * Many methods here are supposed to be optimized for performance.
   *
   * Less code does not always result in better performance.
   * Instead this class is optimized to behave lazy and keep its data close.
   *
   * Keep that in mind in case you ge confused by redundancies in this class.
   * Profile carefully if you want to optimize something.
   *
   * Currently the class is supposed to give best performance for
   * invocations of I18n::translate when
   * - requested translation for current locale exists
   * - translations for current locale are cached
   * - translations for current locale are cached localy
   *
   * This optimal state should be reached after the second invocation of
   * I18n::translate after a certain locale has been pushed for the first
   * time.
   *
   * At the same time the class is supposed to do nothing at all as long
   * as I18n::translate has not been invoked. This lazy behavior is for the
   * sake of performance of e.g. REST services and or scriptlets that may
   * not require any internationalization. Yet they are able to utilize it
   * on demand.
   *
   * There is also a fallback mechanism that delivers a translation of
   * current locale's language if no explicit translation for current
   * locale exists. This fallback is again implemented in favor of performance
   * and in expense of cached data volume.
   *
   * @package net.evalcode.components
   * @subpackage i18n
   *
   * @author evalcode.net
   */
  class I18n
  {
    // PREDEFINED PROPERTIES
    const CACHE_KEY='i18n';
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @return Components\I18n_Locale
     */
    public static function locale()
    {
      return self::$m_locale;
    }

    /**
     * @param Components\I18n_Locale $context_
     *
     * @return Components\I18n_Locale
     */
    public static function push(I18n_Locale $locale_)
    {
      array_push(self::$m_locales, $locale_);

      if(false===isset(self::$m_cache[$locale_->name()]))
        self::$m_cache[$locale_->name()]=array();

      self::$m_translations=&self::$m_cache[$locale_->name()];

      return self::$m_locale=end(self::$m_locales);
    }

    /**
     * @return Components\I18n_Locale
     */
    public static function pop()
    {
      $locale=array_pop(self::$m_locales);

      if(self::$m_locale=end(self::$m_locales))
      {
        if(false===isset(self::$m_cache[self::$m_locale->name()]))
          self::$m_cache[self::$m_locale->name()]=array();

        self::$m_translations=&self::$m_cache[self::$m_locale->name()];
      }
      else
      {
        self::$m_locale=null;
      }

      return $locale;
    }

    public static function getCountries()
    {
      if(null===self::$m_countries)
        self::$m_countries=Cache::get(self::CACHE_KEY.'/country');

      if(false===self::$m_countries)
      {
        self::$m_countries=array();

        $xml=new \SimpleXMLElement(file_get_contents(dirname(__DIR__).'/resource/i18n/common/en.xml'));

        self::$m_countries=array();
        foreach($xml->xpath('//common/country/*') as $node)
          self::$m_countries[$node->getName()]=$node->getName();

        Cache::set(self::CACHE_KEY.'/country', self::$m_countries);
      }

      return self::$m_countries;
    }

    public static function getLanguages()
    {
      if(null===self::$m_languages)
        self::$m_languages=Cache::get(self::CACHE_KEY.'/language');

      if(false===self::$m_languages)
      {
        self::$m_languages=array();

        $xml=new \SimpleXMLElement(file_get_contents(dirname(__DIR__).'/resource/i18n/common/en.xml'));

        self::$m_languages=array();
        foreach($xml->xpath('//common/language/*') as $node)
          self::$m_languages[$node->getName()]=$node->getName();

        Cache::set(self::CACHE_KEY.'/language', self::$m_languages);
      }

      return self::$m_languages;
    }

    public static function clear()
    {
      Cache::clear(self::CACHE_KEY);
    }

    /**
     * @param string $key_
     */
    public static function translate($key_)
    {
      if(isset(self::$m_translations[$key_]))
        return self::$m_translations[$key_];

      if(null===self::$m_locale)
        static::push(I18n_Locale::defaultLocale());

      if(false===isset(self::$m_loaded[self::$m_locale->name()]))
      {
        self::load();

        return static::translate($key_);
      }

      return $key_;
    }

    /**
     * @param string $key_
     * @param string.. $arg1_
     */
    public static function translatef($key_, $arg1_=null/*, $arg2_, $arg3_ ..*/)
    {
      $args=func_get_args();
      $key=array_shift($args);
      if(isset(self::$m_translations[$key]))
        return vsprintf(self::$m_translations[$key], $args);

      if(null===self::$m_locale)
        static::push(I18n_Locale::defaultLocale());

      if(false===isset(self::$m_loaded[self::$m_locale->name()]))
      {
        self::load();

        array_unshift($args, $key);

        return static::translatevf($args);
      }

      return $key;
    }

    /**
     * @param array|string $args_
     */
    public static function translatevf(array $args_)
    {
      $key=array_shift($args_);
      if(isset(self::$m_translations[$key]))
        return vsprintf(self::$m_translations[$key], $args_);

      if(null===self::$m_locale)
        static::push(I18n_Locale::defaultLocale());

      if(false===isset(self::$m_loaded[self::$m_locale->name()]))
      {
        self::load();

        array_unshift($args_, $key);

        return static::translatevf($args_);
      }

      return $key;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var array|string
     */
    private static $m_cache=array();
    /**
     * @var array|string
     */
    private static $m_translations=array();
    /**
     * @var array|boolean
     */
    private static $m_loaded=array();
    /**
     * @var array|Components\I18n_Locale
     */
    private static $m_locales=array();
    /**
     * @var Components\I18n_Locale
     */
    private static $m_locale;
    /**
     * @var array|string
     */
    private static $m_countries;
    /**
     * @var array|string
     */
    private static $m_languages;
    //------


    private static function load()
    {
      $locale=self::$m_locale->name();
      $language=self::$m_locale->language();

      if(false===isset(self::$m_loaded[$locale]))
      {
        if(self::$m_cache[$locale]=Cache::get(self::CACHE_KEY."/$locale"))
          return self::$m_loaded[$locale]=true;

        self::$m_cache[$locale]=array();

        if(false===isset(self::$m_loaded[$language]))
        {
          if(self::$m_cache[$language]=Cache::get(self::CACHE_KEY."/$language"))
            return self::$m_loaded[$language]=true;

          self::$m_cache[$language]=array();

          $directoryIterator=new \RecursiveDirectoryIterator(Environment::pathComponents(),
            \RecursiveDirectoryIterator::SKIP_DOTS|\RecursiveDirectoryIterator::FOLLOW_SYMLINKS
          );

          $iterator=new \RegexIterator(new \RecursiveIteratorIterator($directoryIterator),
            '/\/([a-zA-Z0-9]+)\/resource\/i18n\/((?>[a-zA-Z0-9\/]+\/)|(?R))*([_a-zA-Z]+)\.xml$/',
            \RegexIterator::GET_MATCH
          );

          foreach($iterator as $path=>$match)
            self::loadFile($path, $match[3]);

          foreach(self::$m_cache as $loc=>$translations)
          {
            $l=I18n_Locale::forName($loc);
            if($loc!==$l->language() && isset(self::$m_cache[$l->language()]))
              self::$m_cache[$loc]=array_merge(self::$m_cache[$l->language()], self::$m_cache[$loc]);

            Cache::set(self::CACHE_KEY."/$loc", self::$m_cache[$loc]);

            self::$m_loaded[$loc]=true;
          }

          self::$m_loaded[$language]=true;
        }

        if(false===isset(self::$m_loaded[$locale]) && isset(self::$m_loaded[$language]))
        {
          self::$m_cache[$locale]=array_merge(self::$m_cache[$language], self::$m_cache[$locale]);

          Cache::set(self::CACHE_KEY."/$locale", self::$m_cache[$locale]);

          self::$m_loaded[$locale]=true;
        }
      }
    }

    private static function loadFile($path_, $locale_)
    {
      if($xml=new \SimpleXMLElement(file_get_contents($path_)))
        self::loadNamespace($xml, $locale_);
    }

    private static function loadNamespace(\SimpleXMLElement $node_, $locale_, $namespace_=null)
    {
      if(0<$node_->count())
      {
        foreach($node_->children() as $node)
        {
          if(null===$namespace_)
            self::loadNamespace($node, $locale_, $node_->getName());
          else
            self::loadNamespace($node, $locale_, "$namespace_/{$node_->getName()}");
        }
      }
      else
      {
        if(null===$namespace_)
          self::$m_cache[$locale_][$node_->getName()]=(string)$node_;
        else
          self::$m_cache[$locale_]["$namespace_/{$node_->getName()}"]=(string)$node_;
      }
    }
    //--------------------------------------------------------------------------
  }
?>
