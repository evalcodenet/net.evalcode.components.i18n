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
    const de='de';
    const de_DE='de_DE';
    const zh='zh';
    const zh_CN='zh_CN';
    const zh_Hans_CN='zh_Hans_CN';
    const zh_Hant_CN='zh_Hant_CN';

    const SCRIPT_DEFAULT='latn';
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $name_
     *
     * @return Components\I18n_Locale
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
     * @see Components.Enumeration::values()
     */
    public static function values()
    {
      return array_values(self::$m_locales);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function getName()
    {
      return $this->m_name;
    }

    public function getCountry()
    {
      if(null===$this->m_country)
      {
        $chunks=explode('_', $this->m_name);
        $this->m_country=strtolower(end($chunks));
      }

      return $this->m_country;
    }

    public function getCountryTitle()
    {
      return translate('common/country/'.$this->getCountry());
    }

    public function getLanguage()
    {
      if(null===$this->m_language)
      {
        $chunks=explode('_', $this->m_name);
        $this->m_language=strtolower(reset($chunks));
      }

      return $this->m_language;
    }

    public function getLanguageTitle()
    {
      return translate('common/language/'.$this->getLanguage());
    }

    public function getScript()
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

    public function getScriptTitle()
    {
      return translate('common/script/'.$this->getScript());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var array|string
     */
    private static $m_locales=array(
      self::en=>self::en,
      self::en_US=>self::en_US,
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
    //--------------------------------------------------------------------------
  }
?>
