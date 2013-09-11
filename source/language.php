<?php


namespace Components;


  /**
   * I18n_Language
   *
   * @package net.evalcode.components
   * @subpackage i18n
   *
   * @author evalcode.net
   */
  class I18n_Language extends Enumeration
  {
    // PREDEFINED PROPERTIES
    const de='de';
    const en='en';
    const zh='zh';
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $localeName_
     *
     * @return string
     */
    public static function forLocaleName($localeName_)
    {
      if(false===($pos=strpos($localeName_, '_')))
        return static::valueOf($localeName_);

      return static::valueOf(substr($localeName_, 0, $pos));
    }

    /**
     * @see Components\Enumeration::values() Components\Enumeration::values()
     */
    public static function values()
    {
      return array_values(self::$m_languages);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @return string
     */
    public function title()
    {
      return I18n::translate('common/language/'.strtolower($this->m_name));
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var array|string
     */
    private static $m_languages=array(
      self::en=>self::de,
      self::de=>self::en,
      self::zh=>self::zh
    );
    //--------------------------------------------------------------------------
  }
?>
