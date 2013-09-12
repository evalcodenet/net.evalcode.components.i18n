<?php


namespace Components;


  /**
   * I18n_Country
   *
   * @api
   * @package net.evalcode.components.i18n
   *
   * @author evalcode.net
   *
   * @method \Components\I18n_Country CN
   * @method \Components\I18n_Country DE
   * @method \Components\I18n_Country GB
   * @method \Components\I18n_Country US
   */
  class I18n_Country extends I18n_Location
  {
    // PREDEFINED PROPERTIES
    const CN='CN';
    const DE='DE';
    const GB='GB';
    const US='US';
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($name_, array $data_=null)
    {
      parent::__construct($name_, $data_);

      $this->m_translationKeyTitle='common/country/'.strtolower($name_);
    }
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
     * @return string[]
     */
    public static function values()
    {
      return self::$m_countries;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\I18n_Location::type() \Components\I18n_Location::type()
     */
    public function type()
    {
      return I18n_Location_Type::COUNTRY();
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var string[]
     */
    private static $m_countries=array(
      self::CN,
      self::DE,
      self::GB,
      self::US
    );
    //----


    /**
     * @see \Components\I18n_Location::initialized() \Components\I18n_Location::initialized()
     *
     * @return \Components\I18n_Country
     */
    protected function initialized()
    {
      if(null===$this->m_data)
      {
        $name=strtolower($this->m_name);

        if(false===($this->m_data=Cache::get("i18n/country/$name")))
        {
          $this->m_data['children']=array();
          $path=Io::pathComponentResource('i18n', 'resource', 'i18n', 'location', $name);

          if($path->exists())
          {
            foreach($path as $pathRegion)
            {
              $pathRegionAsString=$pathRegion->getPath();

              if(false===is_file($pathRegionAsString))
                continue;

              $region=basename($pathRegionAsString);
              $region=strtolower(substr($region, 0, strrpos($region, '.')));

              $this->m_data['children'][$region]=null;
            }
          }

          Cache::set("i18n/country/$name", $this->m_data);
        }
      }

      return $this;
    }
    //--------------------------------------------------------------------------
  }
?>
