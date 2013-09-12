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
   * not require any internationalisation. Yet they are able to utilize it
   * on demand.
   *
   * There is also a fallback mechanism that delivers a translation of
   * current locale's language if no explicit translation for current
   * locale exists. This fallback is again implemented in favor of performance
   * and in expense of cached data volume.
   *
   * @api
   * @package net.evalcode.components.i18n
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
     * @return \Components\I18n_Locale
     */
    public static function locale()
    {
      if(null===self::$m_locale)
        static::push(I18n_Locale::defaultLocale());

      return self::$m_locale;
    }

    /**
     * @param \Components\I18n_Locale $context_
     *
     * @return \Components\I18n_Locale
     */
    public static function push(I18n_Locale $locale_)
    {
      array_push(self::$m_locales, $locale_);

      if(false===isset(self::$m_cache[$locale_->name()]))
        self::$m_cache[$locale_->name()]=array();

      self::$m_translations=&self::$m_cache[$locale_->name()];

      setlocale(LC_ALL, $locale_->systemLocale());

      return self::$m_locale=end(self::$m_locales);
    }

    /**
     * @return \Components\I18n_Locale
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

      setlocale(LC_ALL, $locale->systemLocale());

      return $locale;
    }

    public static function countries()
    {
      if(null===self::$m_countries)
        self::$m_countries=Cache::get(self::CACHE_KEY.'/country');

      if(false===self::$m_countries)
      {
        self::$m_countries=array();

        $file=static::pathTranslationCommon()->getFile('en.json');
        $json=(array)json_decode($file->getContent(), true);

        self::$m_countries=array_keys($json['common']['country']);
        self::$m_countries=array_combine(self::$m_countries, self::$m_countries);

        Cache::set(self::CACHE_KEY.'/country', self::$m_countries);
      }

      return self::$m_countries;
    }

    public static function languages()
    {
      if(null===self::$m_languages)
        self::$m_languages=Cache::get(self::CACHE_KEY.'/language');

      if(false===self::$m_languages)
      {
        self::$m_languages=array();

        $file=static::pathTranslationCommon()->getFile('en.json');
        $json=(array)json_decode($file->getContent(), true);

        self::$m_languages=array_keys($json['common']['language']);
        self::$m_languages=array_combine(self::$m_languages, self::$m_languages);

        Cache::set(self::CACHE_KEY.'/language', self::$m_languages);
      }

      return self::$m_languages;
    }

    public static function clearCache()
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
     * @param string[] $args_
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
     * @var string[]
     */
    private static $m_cache=array();
    /**
     * @var string[]
     */
    private static $m_translations=array();
    /**
     * @var boolean[]
     */
    private static $m_loaded=array();
    /**
     * @var \Components\I18n_Locale[]
     */
    private static $m_locales=array();
    /**
     * @var \Components\I18n_Locale
     */
    private static $m_locale;
    /**
     * @var string[]
     */
    private static $m_countries;
    /**
     * @var string[]
     */
    private static $m_languages;
    /**
     * @var \Components\Io_Path
     */
    private static $m_pathTranslationCommon;
    //------


    private static function load()
    {
      $locale=self::$m_locale->name();
      $language=self::$m_locale->languageName();

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
            '/\/([a-zA-Z0-9]+)\/resource\/i18n\/translation\/((?>[a-zA-Z0-9\/]+\/)|(?R))*([_a-zA-Z]+)\.json$/',
              \RegexIterator::GET_MATCH
          );

          foreach($iterator as $path=>$match)
            self::loadFile($path, $match[3]);

          foreach(self::$m_cache as $loc=>$translations)
          {
            $l=I18n_Locale::forName($loc);
            $ll=$l->languageName();

            if($loc!==$ll && isset(self::$m_cache[$ll]))
              self::$m_cache[$loc]=array_merge(self::$m_cache[$ll], self::$m_cache[$loc]);

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
      $translations=(array)json_decode(file_get_contents($path_), true);

      self::loadNamespace($translations, $locale_);
    }

    private static function loadNamespace(array $translations_, $locale_, $namespace_=null)
    {
      foreach($translations_ as $key=>$value)
      {
        if(is_array($value))
        {
          if(null===$namespace_)
            self::loadNamespace($value, $locale_, $key);
          else
            self::loadNamespace($value, $locale_, "$namespace_/$key");
        }
        else
        {
          if(null===$namespace_)
            self::$m_cache[$locale_][$key]=$value;
          else
            self::$m_cache[$locale_]["$namespace_/$key"]=$value;
        }
      }
    }

    /**
     * @return \Components\Io_Path
     */
    private static function pathTranslationCommon()
    {
      if(null===self::$m_pathTranslationCommon)
        self::$m_pathTranslationCommon=Io::pathComponentResource('i18n', 'resource', 'i18n', 'translation', 'common');

      return self::$m_pathTranslationCommon;
    }
    //--------------------------------------------------------------------------
  }
?>
