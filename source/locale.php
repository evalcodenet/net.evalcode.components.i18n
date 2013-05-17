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
   * @method Components\I18n_Locale en
   * @method Components\I18n_Locale en_US
   * @method Components\I18n_Locale de
   * @method Components\I18n_Locale de_DE
   * @method Components\I18n_Locale zh
   * @method Components\I18n_Locale zh_CN
   * @method Components\I18n_Locale zh_Hans_CN
   * @method Components\I18n_Locale zh_Hant_CN
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

    const SCRIPT_DEFAULT='latn';

    const LOCALE_DEFAULT=self::en_US;
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
     * @see Components\Enumeration::values()
     */
    public static function values()
    {
      return array_values(self::$m_locales);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function title()
    {
      return $this->language().', '.$this->country();
    }

    public function country()
    {
      if(null===$this->m_country)
      {
        $chunks=explode('_', $this->m_name);
        $this->m_country=strtolower(end($chunks));
      }

      return $this->m_country;
    }

    public function countryTitle()
    {
      return I18n::translate('common/country/'.$this->country());
    }

    public function language()
    {
      if(null===$this->m_language)
      {
        $chunks=explode('_', $this->m_name);
        $this->m_language=strtolower(reset($chunks));
      }

      return $this->m_language;
    }

    public function languageTitle()
    {
      return I18n::translate('common/language/'.$this->language());
    }

    public function script()
    {
      if(null===$this->m_script)
      {
        $chunks=explode('_', $this->m_name);

        if(3===count($chunks))
          $this->m_script=strtolower($chunks[1]);
        else
          $this->m_script=self::SCRIPT_DEFAULT;
      }

      return $this->m_script;
    }

    public function scriptTitle()
    {
      return I18n::translate('common/script/'.$this->script());
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
        $this->m_systemLocale=self::$m_systemLocales[$this->m_name];

      return $this->m_systemLocale;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    // TODO Configurable, determine available locales, print warning for unavailable etc.
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
    // TODO Configurable
    /**
     * @var array|string
     */
    // TODO Determine available locales
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

    private $m_language;
    private $m_country;
    private $m_script;
    private $m_charset;
    private $m_systemLocale;
    //--------------------------------------------------------------------------
  }
?>
