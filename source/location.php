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
  class I18n_Location implements Object, Value_String
  {
    // CONSTRUCTION
    public function __construct($value_)
    {
      $this->m_value=$value_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @param string $value_
     *
     * @return \Components\I18n_Location
     */
    public static function valueOf($value_)
    {
      return new self($value_);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @return \Components\Point
     */
    public function position()
    {
      return $this->initialize()->m_position;
    }

    public function cast()
    {
      return $this->initialize()->m_impl;
    }

    public function title()
    {
      // FIXME Merge into common translations
      return $this->initialize()->m_data['title'][I18n::locale()->languageName()];
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function value()
    {
      return $this->m_value;
    }

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
    private static $m_types=array(
      2=>'\\Components\\I18n_Region',
      3=>'\\Components\\I18n_City',
      4=>'\\Components\\I18n_District'
    );

    /**
     * @var boolean
     */
    private $m_initialized=false;
    /**
     * @var string
     */
    private $m_data=array();
    /**
     * @var string
     */
    private $m_value;
    /**
     * @var \Components\Point
     */
    private $m_position;
    /**
     * @var \Components\I18n_Location_Concrete
     */
    private $m_impl;
    //-----


    protected function initialize()
    {
      if(false===$this->m_initialized)
      {
        if(false===($this->m_data=Cache::get("i18n/location/{$this->m_value}")))
        {
          $file=null;
          $sub=array();
          $path=Io::path(Environment::pathComponentResource('i18n', 'resource', 'i18n', 'location'));

          $chunks=explode('_', $this->m_value);
          while(count($chunks))
          {
            $file=$path->getFile(implode('/', $chunks).'.json');

            if($file->exists())
              break;

            array_push($sub, array_pop($chunks));
          }

          if(false===$file->exists())
            return $this;

          $json=$file->getContent();
          $value=json_decode($json, true);

          $this->m_data=$value;

          if(0<count($sub))
          {
            while($next=array_shift($sub))
              $this->m_data=&$this->m_data[$next];
          }

          Cache::set("i18n/location/{$this->m_value}", $this->m_data);
        }

        if(isset($this->m_data['latitude']) && $this->m_data['longitude'])
          $this->m_position=Point::of($this->m_data['latitude'], $this->m_data['longitude']);

        $type=self::$m_types[$this->m_data['type']];
        $this->m_impl=new $type($this);

        $this->m_initialized=true;
      }

      return $this;
    }
    //--------------------------------------------------------------------------
  }
?>
