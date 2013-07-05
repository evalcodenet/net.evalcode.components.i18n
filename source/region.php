<?php


namespace Components;


  /**
   * I18n_Region
   *
   * @package net.evalcode.components
   * @subpackage i18n
   *
   * @author evalcode.net
   */
  class I18n_Region implements Object
  {
    // CONSTRUCTION
    public function __construct($name_)
    {
      $this->m_name=$name_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @return string
     */
    public function name()
    {
      return $this->m_name;
    }

    /**
     * @return string
     */
    public function title()
    {
      return I18n::translate('common/script/'.strtolower($this->m_name));
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
    public function cities()
    {

    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
    * (non-PHPdoc)
    * @see \Components\Object::hashCode()
    */
    public function hashCode()
    {
      return string_hash($this->m_name);
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_name===$object_->m_name;

      return false;
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{name: %s, location: %s}',
        __CLASS__,
        $this->hashCode(),
        $this->m_name,
        $this->m_location
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var string
     */
    private $m_name;
    /**
     * @var \Components\I18n_Location
     */
    private $m_location;
    //--------------------------------------------------------------------------
  }
?>
