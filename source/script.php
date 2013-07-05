<?php


namespace Components;


  /**
   * I18n_Script
   *
   * @package net.evalcode.components
   * @subpackage i18n
   *
   * @author evalcode.net
   *
   * @method \Components\I18n_Script_Hans Hans
   * @method \Components\I18n_Script Hant
   * @method \Components\I18n_Script Latn
   */
  class I18n_Script extends Enumeration
  {
    // PREDEFINED PROPERTIES
    const Hans='Hans';
    const Hant='Hant';
    const Latn='Latn';
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @see Components\Enumeration::values()
     */
    public static function values()
    {
      return array_values(self::$m_scripts);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @return string
     */
    public function title()
    {
      return I18n::translate('common/script/'.strtolower($this->m_name));
    }

    /**
     * Transforms passed string to script of given instance.
     *
     * @param I18n_Script $script_
     * @param string $string_
     *
     * @return string
     *
     * @throws Exception_NotSupported If requested transformation is not
     * supported by this script.
     */
    public function transformTo(I18n_Script $script_, $string_)
    {
      return $this->{'transformTo'.$script_->name()}($string_);
    }

    /**
     * Transforms passed string to ASCII.
     *
     * @param string $string_
     *
     * @return string
     */
    public function transformToAscii($string_)
    {
      return \Components\String::toAscii($this->transformToLatn($string_));
    }

    /**
     * Transforms passed string to LATIN.
     *
     * @param string $string_
     *
     * @return string
     */
    public function transformToLatn($string_)
    {
      return $string_;
    }

    /**
     * Detect whether passed string contains characters of this script.
     *
     * @param string $string_
     *
     * @return boolean
     */
    public function detect($string_)
    {
      // TODO Verify correctness.
      return \Components\String::isLatin1($string_);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function __call($name_, array $args_=array())
    {
      $script=array_shift($args_);

      if($script instanceof self)
      {
        throw new Exception_NotSupported('i18n/script', sprintf(
          'Transformation from %s to %s is not supported.', $this, reset($args_)
        ));
      }

      throw new Exception_NotImplemented('i18n/script', sprintf(
        'I18n_Script::%s is not implemented.', $name_
      ));
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var array|string
     */
    private static $m_scripts=array(
      self::Hans=>self::Hans,
      self::Hant=>self::Hant,
      self::Latn=>self::Latn
    );
    //--------------------------------------------------------------------------
  }
?>
