<?php


namespace Components;


  /**
   * I18n_Locale
   *
   * @package net.evalcode.components
   * @subpackage i18n
   *
   * @author evalcode.net
   *
   * @method \Components\I18n_Locale valueOf
   * @method \Components\I18n_Locale en
   * @method \Components\I18n_Locale en_US
   * @method \Components\I18n_Locale en_GB
   * @method \Components\I18n_Locale de
   * @method \Components\I18n_Locale de_DE
   * @method \Components\I18n_Locale zh
   * @method \Components\I18n_Locale zh_CN
   * @method \Components\I18n_Locale zh_Hans_CN
   * @method \Components\I18n_Locale zh_Hant_CN
   */
  class I18n_Locale extends Enumeration
  {
    // PREDEFINED PROPERTIES
    const en='en';
    const en_US='en_US';
    const en_GB='en_GB';
    const de='de';
    const de_DE='de_DE';
    const zh='zh';
    const zh_CN='zh_CN';
    const zh_Hans_CN='zh_Hans_CN';
    const zh_Hant_CN='zh_Hant_CN';

    const LOCALE_DEFAULT=self::en_US;
    const LANGUAGE_DEFAULT=I18n_Language::en;
    const COUNTRY_DEFAULT=I18n_Country::US;
    const SCRIPT_DEFAULT=I18n_Script::Latn;
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $name_
     *
     * @return \Components\I18n_Locale
     */
    public static function forName($name_)
    {
      if(isset(self::$m_locales[$name_]))
      {
        $locale=self::$m_locales[$name_];

        return self::$locale();
      }

      return null;
    }

    /**
     * @return \Components\I18n_Locale
     */
    public static function defaultLocale()
    {
      $locale=self::LOCALE_DEFAULT;

      return self::$locale();
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Enumeration::values()
     */
    public static function values()
    {
      return array_values(self::$m_locales);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @return string
     */
    public function title()
    {
      if(null===$this->m_title)
        $this->m_title=$this->language()->title().', '.$this->country()->title();

      return $this->m_title;
    }

    /**
     * @return \Components\I18n_Country
     */
    public function country()
    {
      if(null===$this->m_country)
        $this->m_country=I18n_Country::valueOf($this->countryName());

      return $this->m_country;
    }

    /**
     * @return string
     *
     * @throws Exception_NotSupported
     */
    public function countryName()
    {
      if(null===$this->m_countryName)
      {
        if(false===strpos($this->m_name, '_'))
        {
          if(isset(self::$m_countries[$this->m_name]))
            $this->m_countryName=self::$m_countries[$this->m_name];
          else
            $this->m_countryName=self::COUNTRY_DEFAULT;
        }
        else
        {
          $chunks=explode('_', $this->m_name);
          $this->m_countryName=end($chunks);
        }
      }

      return $this->m_countryName;
    }

    /**
     * @return \Components\I18n_Language
     */
    public function language()
    {
      if(null===$this->m_language)
        $this->m_language=I18n_Language::valueOf($this->languageName());

      return $this->m_language;
    }

    /**
     * @return string
     */
    public function languageName()
    {
      if(null===$this->m_languageName)
      {
        $chunks=explode('_', $this->m_name);
        $this->m_languageName=reset($chunks);
      }

      return $this->m_languageName;
    }

    /**
     * @return \Components\I18n_Script
     */
    public function script()
    {
      if(null===$this->m_script)
        $this->m_script=I18n_Script::valueOf($this->scriptName());

      return $this->m_script;
    }

    /**
     * @return string
     */
    public function scriptName()
    {
      if(null===$this->m_scriptName)
      {
        $chunks=explode('_', $this->m_name);

        if(3===count($chunks))
          $this->m_scriptName=$chunks[1];
        else if(isset(self::$m_scripts[$this->languageName()]))
          $this->m_scriptName=self::$m_scripts[$this->m_languageName];
        else
          $this->m_scriptName=self::SCRIPT_DEFAULT;
      }

      return $this->m_scriptName;
    }

    /**
     * @return \Components\Io_Charset
     */
    public function charset()
    {
      if(null===$this->m_charset)
        $this->m_charset=Io_Charset::forName(self::$m_charsets[$this->m_name]);

      return $this->m_charset;
    }

    /**
     * @return string
     */
    public function systemLocale()
    {
      if(null===$this->m_systemLocale)
        $this->m_systemLocale=self::$m_systemLocales[$this->languageName().'_'.$this->countryName()];

      return $this->m_systemLocale;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var array|string
     */
    private static $m_locales=array(
      self::en=>self::en,
      self::en_US=>self::en_US,
      self::en_GB=>self::en_GB,
      self::de=>self::de,
      self::de_DE=>self::de_DE,
      self::zh=>self::zh,
      self::zh_CN=>self::zh_CN,
      self::zh_Hans_CN=>self::zh_Hans_CN,
      self::zh_Hant_CN=>self::zh_Hant_CN
    );
    // TODO Configurable defaults
    // TODO Determine available locales
    /**
     * @var array|string
     */
    private static $m_systemLocales=array(
      self::en=>'en_US.utf8',
      self::en_US=>'en_US.utf8',
      self::en_GB=>'en_GB.utf8',
      self::de=>'de_DE.utf8',
      self::de_DE=>'de_DE.utf8',
      self::zh=>'zh_CN.utf8',
      self::zh_CN=>'zh_CN.utf8'
    );
    // TODO Configurable defaults
    // TODO Determine available charsets
    /**
     * @var array|string
     */
    private static $m_charsets=array(
      self::en=>Io_Charset::UTF_8,
      self::en_US=>Io_Charset::UTF_8,
      self::en_GB=>Io_Charset::UTF_8,
      self::de=>Io_Charset::UTF_8,
      self::de_DE=>Io_Charset::UTF_8,
      self::zh=>Io_Charset::UTF_8,
      self::zh_CN=>Io_Charset::UTF_8
    );
    /**
     * @var array|string
     */
    private static $m_countries=array(
      self::de=>I18n_Country::DE,
      self::en=>I18n_Country::US,
      self::zh=>I18n_Country::CN
    );
    /**
     * @var array|string
     */
    private static $m_scripts=array(
      self::de=>I18n_Script::Latn,
      self::en=>I18n_Script::Latn,
      self::zh=>I18n_Script::Hans
    );

    private $m_title;
    private $m_charset;
    private $m_country;
    private $m_countryName;
    private $m_language;
    private $m_languageName;
    private $m_script;
    private $m_scriptName;
    private $m_systemLocale;
    //--------------------------------------------------------------------------
  }
?>
