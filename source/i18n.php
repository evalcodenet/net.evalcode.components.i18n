<?php


namespace Components;


  /**
   * I18n
   *
   * @package net.evalcode.components
   * @subpackage runtime
   *
   * @author evalcode.net
   */
  class I18n
  {
    // PREDEFINED PROPERTIES
    const CACHE_KEY='i18n';
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct(I18n_Locale $locale_)
    {
      $this->m_locale=$locale_;

      $locale=$locale_->getName();
      $language=$locale_->getLanguage();

      if(isset(self::$m_cache[$locale]))
      {
        $this->m_translations=&self::$m_cache[$locale];
        $this->m_translationsFallback=&self::$m_cache[$language];
      }
      else
      {
        $this->m_translations=&self::$m_cache[$language];
        $this->m_translationsFallback=array();
      }
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @return I18n
     */
    public static function current()
    {
      return self::$m_current;
    }

    /**
     * @param I18n $context_
     *
     * @return I18n
     */
    public static function push(I18n $context_)
    {
      array_push(self::$m_instances, $context_);

      return self::$m_current=end(self::$m_instances);
    }

    /**
     * @return I18n
     */
    public static function pop()
    {
      $context=array_pop(self::$m_instances);
      self::$m_current=end(self::$m_instances);

      return $context;
    }

    public static function clear()
    {
      Cache::clear(self::CACHE_KEY);
    }

    public static function load()
    {
      if(null===self::$m_cache)
        self::$m_cache=Cache::get(self::CACHE_KEY);

      if(null===self::$m_cache)
      {
        self::$m_cache=array();

        $directoryIterator=new \RecursiveDirectoryIterator(
          Environment::pathComponents(),
          \RecursiveDirectoryIterator::SKIP_DOTS|\RecursiveDirectoryIterator::FOLLOW_SYMLINKS
        );

        $iterator=new \RegexIterator(new \RecursiveIteratorIterator($directoryIterator),
          '/\/([a-zA-Z0-9]+)\/resource\/i18n\/((?>[a-zA-Z0-9\/]+\/)|(?R))*([_a-zA-Z]+)\.xml$/',
          \RegexIterator::GET_MATCH
        );

        foreach($iterator as $path=>$match)
          self::loadFile($path, $match[3]);

        Cache::set(self::CACHE_KEY, self::$m_cache);
      }
    }

    public static function getCountries()
    {
      if(null===self::$m_countries)
        self::$m_countries=Cache::get(self::CACHE_KEY.'/country');

      if(null===self::$m_countries)
      {
        self::$m_countries=array();

        $xml=new \SimpleXMLElement(@file_get_contents(dirname(__DIR__).'/resource/i18n/common/en.xml'));

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

      if(null===self::$m_languages)
      {
        self::$m_languages=array();

        $xml=new \SimpleXMLElement(@file_get_contents(dirname(__DIR__).'/resource/i18n/common/en.xml'));

        self::$m_languages=array();
        foreach($xml->xpath('//common/language/*') as $node)
          self::$m_languages[$node->getName()]=$node->getName();

        Cache::set(self::CACHE_KEY.'/language', self::$m_languages);
      }

      return self::$m_languages;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @return I18n_Locale
     */
    public function getLocale()
    {
      return $this->m_locale;
    }

    /**
     * @param string $key_
     */
    public function translate($key_)
    {
      if($value=@$this->m_translations[$key_])
        return $value;

      if($value=@$this->m_translationsFallback[$key_])
        return $value;

      return $key_;
    }

    /**
     * @param string... $key_
     */
    public function translatef(array $args_)
    {
      $key=array_shift($args_);
      if($value=@$this->m_translations[$key])
        return call_user_func_array('sprintf', array_merge(array($value), $args_));

      if($value=@$this->m_translationsFallback[$key])
        return call_user_func_array('sprintf', array_merge(array($value), $args_));

      return $key;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var array|I18n
     */
    private static $m_instances=array();
    /**
     * @var I18n
     */
    private static $m_current;
    /**
     * @var null|array|string
     */
    private static $m_cache;
    /**
     * @var null|array|string
     */
    private static $m_countries;
    /**
     * @var null|array|string
     */
    private static $m_languages;

    /**
     * @var I18n_Locale
     */
    private $m_locale;

    /**
    private $m_translations=array();
    /**
     * @var array|string
     */
    private $m_translationsFallback=array();
    //------


    private static function loadFile($path_, $locale_)
    {
      if($xml=new \SimpleXMLElement(@file_get_contents($path_)))
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


  // GLOBAL HELPERS
  /**
   * @param string $key_
   */
  function translate($key_)
  {
    return I18n::current()->translate($key_);
  }

  /**
   * @param string... $key_
   */
  function translatef($key_/*, $arg0_, $arg1_...*/)
  {
    return I18n::current()->translatef(func_get_args());
  }
?>
