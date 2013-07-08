<?php


namespace Components;


  /**
   * I18n_Country
   *
   * @package net.evalcode.components
   * @subpackage i18n
   *
   * @author evalcode.net
   */
  class I18n_Country extends Enumeration
  {
    // PREDEFINED PROPERTIES
    const CN='CN';
    const DE='DE';
    const GB='GB';
    const US='US';
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $localeName_
     *
     * @return string
     */
    public static function forLocaleName($localeName_)
    {
      if(false===($pos=strrpos($localeName_, '_')))
        return null;

      return static::valueOf(substr($localeName_, $pos+1));
    }

    /**
     * @see Components\Enumeration::values()
     */
    public static function values()
    {
      return array_values(self::$m_countries);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @return string
     */
    public function title()
    {
      return I18n::translate('common/country/'.strtolower($this->m_name));
    }

    /**
     * @return array|string
     */
    public function cities()
    {

    }

    /**
     * @return array|string
     */
    public function regions()
    {

    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var array|string
     */
    private static $m_countries=array(
      self::CN=>self::CN,
      self::DE=>self::DE,
      self::GB=>self::GB,
      self::US=>self::US
    );
    //--------------------------------------------------------------------------
  }
?>
