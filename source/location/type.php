<?php


namespace Components;


  /**
   * I18n_Location_Type
   *
   * @package net.evalcode.components
   * @subpackage i18n.location
   *
   * @author evalcode.net
   *
   * @method \Components\I18n_Location_Type WORLD
   * @method \Components\I18n_Location_Type CONTINENT
   * @method \Components\I18n_Location_Type COUNTRY
   * @method \Components\I18n_Location_Type REGION
   * @method \Components\I18n_Location_Type CITY
   * @method \Components\I18n_Location_Type DISTRICT
   */
  class I18n_Location_Type extends Enumeration
  {
    // PREDEFINED PROPERTIES
    const WORLD='world';
    const CONTINENT='continent';
    const COUNTRY='country';
    const REGION='region';
    const CITY='city';
    const DISTRICT='district';
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**     * @see \Components\Enumeration::values() \Components\Enumeration::values()
     */
    public static function values()
    {
      return self::$m_types;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @return string
     */
    public function title()
    {
      return I18n::translate('common/location/type/'.strtolower($this->m_name));
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_types=array(
      'WORLD',
      'CONTINENT',
      'COUNTRY',
      'REGION',
      'CITY',
      'DISTRICT'
    );
    //--------------------------------------------------------------------------
  }
?>
