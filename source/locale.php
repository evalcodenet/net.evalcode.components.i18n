<?php


  /**
   * I18n_Locale
   *
   * @package net.evalcode.components
   * @subpackage runtime.i18n
   *
   * @author evalcode.net
   *
   * @method I18n_Locale en
   * @method I18n_Locale en_US
   * @method I18n_Locale de
   * @method I18n_Locale de_DE
   * @method I18n_Locale zh
   * @method I18n_Locale zh_CN
   * @method I18n_Locale zh_Hans_CN
   * @method I18n_Locale zh_Hant_CN
   */
  class I18n_Locale
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


    // CONSTRUCTION
    public function __construct($name_)
    {
      $this->m_name=$name_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    public static function __callStatic($name_, array $args_=array())
    {
      if(null===constant("I18n_Locale::$name_"))
        throw new Runtime_Exception('i18n/locale', sprintf('Unknown locale [name: %1$s].', $name_));

      return new static($name_);
    }

    /**
     * @param string $name_
     *
     * @return I18n_Locale
     */
    public static function forName($name_)
    {
      return static::$name_();
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
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


    // OVERRIDES/IMPLEMENTS
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_name===$object_->m_name;

      return false;
    }

    public function hashCode()
    {
      $hash=0;

      $len=strlen($this->m_name);
      for($i=0; $i<$len; $i++)
        $hash=31*$hash+ord($this->m_name[$i]);

      return $hash;
    }

    public function __toString()
    {
      return $this->m_name;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_name;
    private $m_language;
    private $m_country;
    private $m_script;
    //--------------------------------------------------------------------------
  }
?>
