<?php


namespace Components;


  /**
   * I18n_Location
   *
   * @api
   * @package net.evalcode.components.i18n
   *
   * @author evalcode.net
   *
   * @property \Componens\I18n_Location parent
   */
  class I18n_Location implements Object, Value_String
  {
    // CONSTRUCTION
    public function __construct($name_, array $data_=null)
    {
      $this->m_name=$name_;
      $this->m_data=$data_;
      $this->m_translationKeyTitle="common/location/$name_";
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $value_
     *
     * @return \Components\I18n_Location
     */
    public static function valueOf($name_)
    {
      if(false===isset(self::$m_instance[$name_]))
        self::$m_instance[$name_]=new static($name_);

      return self::$m_instance[$name_];
    }

    /**
     * @return \Components\I18n_Location
     */
    public static function __callStatic($name_, array $args_=array())
    {
      return static::valueOf($name_);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
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
      return I18n::translate($this->m_translationKeyTitle);
    }

    /**
     * @return \Components\I18n_Location_Type
     */
    public function type()
    {
      if(null===$this->m_type)
      {
        if(isset($this->initialized()->m_data['type']))
          $this->m_type=I18n_Location_Type::forKey($this->m_data['type']);
      }

      return $this->m_type;
    }

    /**
     * @return \Components\Point
     */
    public function position()
    {
      if(null===$this->m_position)
      {
        if(isset($this->initialized()->m_data['latitude']) && $this->m_data['longitude'])
          $this->m_position=Point::of($this->m_data['latitude'], $this->m_data['longitude']);
      }

      return $this->m_position;
    }

    /**
     * @return string[]
     */
    public function childNames($sorted_=false)
    {
      if(false===$sorted_)
        return array_keys($this->initialized()->m_data['children']);

      $names=array_keys($this->initialized()->m_data['children']);
      asort($names);

      return $names;
    }

    /**
     * @return string[]
     */
    public function path()
    {
      if(null===$this->m_path)
      {
        if($parent=$this->parent)
          $this->m_path=$parent->path();
        else
          $this->m_path=array();

        $this->m_path[]=$this->m_name;
      }

      return $this->m_path;
    }

    /**
     * @return string[]
     */
    public function titlePath()
    {
      if(null===$this->m_titlePath)
      {
        if($parent=$this->parent)
          $this->m_titlePath=$parent->titlePath().', '.$this->title();
        else
          $this->m_titlePath=$this->title();
      }

      return $this->m_titlePath;
    }

    // TODO Implement Iterator.
    //--------------------------------------------------------------------------


    // OVERRIDES
    public function __get($name_)
    {
      if('parent'===$name_)
      {
        $name=substr($this->m_name, 0, strrpos($this->m_name, '_'));

        if(!$name)
          return null;

        if(false===strpos($name, '_'))
          return I18n_Country::valueOf($name);

        return static::valueOf($name);
      }

      if(false===isset($this->m_children[$name_]))
      {
        if(array_key_exists($name_, $this->initialized()->m_data['children']))
          $this->m_children[$name_]=new self(strtolower($this->m_name."_$name_"), $this->m_data['children'][$name_]);
      }

      if(false===isset($this->m_children[$name_]))
        return null;

      return $this->m_children[$name_];
    }

    /**
     * @see \Components\Value_String::value() \Components\Value_String::value()
     */
    public function value()
    {
      return $this->m_name;
    }

    /**
     * @see \Components\Object::hashCode() \Components\Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**
     * @see \Components\Object::equals() \Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return String::equal($this->m_name, $object_->m_name);

      return false;
    }

    /**
     * @see \Components\Object::__toString() \Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{name: %s, type: %s, position: %s}',
        __CLASS__,
        $this->hashCode(),
        $this->m_name,
        $this->type(),
        $this->position()
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Components\Io_Path
     */
    protected static $m_pathResource;
    /**
     * @var \Components\I18n_Location[]
     */
    private static $m_instance=array();
    /**
     * @var \Components\I18n_Location[]
     */
    protected $m_children=array();
    /**
     * @var string[]
     */
    protected $m_data;
    /**
     * @var string
     */
    protected $m_name;
    /**
     * @var string
     */
    protected $m_translationKeyTitle;
    /**
     * @var \Components\I18n_Location_Type
     */
    protected $m_type;
    /**
     * @var \Components\Point
     */
    protected $m_position;
    /**
     * @var string[]
     */
    private $m_path;
    /**
     * @var string
     */
    private $m_titlePath;
    //-----


    /**
     * @return \Components\I18n_Location
     */
    protected function initialized()
    {
      if(null===$this->m_data)
      {
        if(false===($this->m_data=Cache::get("i18n/location/{$this->m_name}")))
        {
          if(null===self::$m_pathResource)
            self::$m_pathResource=Io::path(Environment::pathComponentResource('i18n', 'resource', 'i18n', 'location'));

          $file=null;

          $sub=array();
          $chunks=explode('_', strtolower($this->m_name));

          while(count($chunks))
          {
            $file=self::$m_pathResource->getFile(implode('/', $chunks).'.json');

            if($file->exists())
              break;

            array_unshift($sub, array_pop($chunks));
          }

          if(false===$file->exists())
          {
            Cache::set("i18n/location/{$this->m_name}", array());

            return $this;
          }

          $json=$file->getContent();
          $this->m_data=json_decode($json, true);

          if(0<count($sub))
          {
            while($next=array_shift($sub))
              $this->m_data=&$this->m_data['children'][$next];
          }

          Cache::set("i18n/location/{$this->m_name}", $this->m_data);
        }
      }

      return $this;
    }
    //--------------------------------------------------------------------------
  }
?>
