<?php


namespace Components;


  /**
   * I18n_City
   *
   * @package net.evalcode.components
   * @subpackage i18n
   *
   * @author evalcode.net
   */
  class I18n_City implements Object
  {
    // CONSTRUCTION
    public function __construct(I18n_Location $location_)
    {
      $this->m_location=$location_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @return string
     */
    public function title()
    {
      return $this->m_location->title();
    }

    /**
     * @return \Components\I18n_Location
     */
    public function location()
    {
      return $this->m_location;
    }

    /**
     * @return array|string
     */
    public function districts()
    {
      return $this->m_location->children();
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
    * (non-PHPdoc)
    * @see \Components\Object::hashCode()
    */
    public function hashCode()
    {
      return string_hash($this->m_location->name());
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_location->equals($object_->m_location);

      return false;
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{location: %s}',
        __CLASS__,
        $this->hashCode(),
        $this->m_location
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Components\I18n_Location
     */
    private $m_location;
    //--------------------------------------------------------------------------
  }
?>
