<?php


namespace Components;


  /**
   * I18n_Location
   *
   * @package net.evalcode.components
   * @subpackage i18n
   *
   * @author evalcode.net
   */
  class I18n_Location implements Object
  {
    // CONSTRUCTION
    public function __construct(Point $position_)
    {
      $this->m_position=$position_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @return Point
     */
    public function position()
    {
      return $this->m_position;
    }

    // TODO Implement
    // /**
    //  * @return Quadrant
    //  */
    // public function quadrant()
    // {
    //   return $this->m_quadrant;
    // }

    // /**
    //  * @param I18n_Location $location_
    //  *
    //  * @return boolean
    //  */
    // public function intersects(I18n_Location $location_)
    // {
    //   return $this->m_quadrant->intersects($location_->m_quadrant);
    // }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * (non-PHPdoc)
     * @see \Components\Object::hashCode()
     */
    public function hashCode()
    {
      return $this->m_position->hashCode();
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_position->equals($object_->m_position);

      return false;
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{position: %s}',
        __CLASS__,
        $this->hashCode(),
        $this->m_position
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Components\Point
     */
    private $m_position;
    //--------------------------------------------------------------------------
  }
?>
