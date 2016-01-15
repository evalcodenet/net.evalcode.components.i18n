<?php


namespace Components;


  /**
   * I18n_Script_Hans
   *
   * @api
   * @package net.evalcode.components.i18n
   * @subpackage script
   *
   * @author evalcode.net
   */
  class I18n_Script_Hans extends I18n_Script
  {
    // PREDEFINED PROPERTIES
    const CACHE_KEY='i18n_script_hans';
    //--------------------------------------------------------------------------


    // PROPERTIES
    public static $unicodeRange=[
      0x4e00, 0x9fff
      // XXX Evaluate if following are required when actual issues come up.
      // 0x3400, 0x4dbf,
      // 0x20000, 0x2a6df,
      // 0x2a700, 0x2b73f,
      // 0x2b740, 0x2b81f,
      // 0x9f00, 0xfaff,
      // 0x2f800, 0x2fa1f
    ];
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\I18n_Script::transformToLatn() transformToLatn
     */
    public function transformToLatn($string_)
    {
      if(null===self::$m_unicode)
        static::load();

      $transformed='';
      $length=mb_strlen($string_);

      for($i=0; $i<$length; $i++)
      {
        $char=mb_substr($string_, $i, 1);
        $dec=Character::unicodeDecimal($char);

        if(isset(self::$m_transformLatn[$dec]))
          $transformed.=self::$m_transformLatn[$dec].' ';
        else
          $transformed.=$char;
      }

      return rtrim($transformed, ' ');
    }

    /**
     * @see \Components\I18n_Script::detect() detect
     */
    public function detect($string_)
    {
      if(null===self::$m_unicode)
        static::load();

      $length=mb_strlen($string_);
      $ranges=array_chunk(static::$unicodeRange, 2);

      foreach($ranges as $range)
      {
        $low=reset($range);
        $high=end($range);

        for($i=0; $i<$length; $i++)
        {
          $dec=Character::unicodeDecimal(mb_substr($string_, $i, 1));

          if($low<=$dec && $high>=$dec)
            return true;
        }
      }

      return false;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_unicode;
    private static $m_transformLatn;
    //-----


    // TODO Too slow. Split map into chunks and load ranges on demand or generate php source to include here ..
    private static function load()
    {
      if(!$map=Cache::get(self::CACHE_KEY.'_map'))
      {
        $path=Environment::pathComponentsResource(
          'i18n', 'resource', 'i18n', 'script', 'hans.json'
        );

        $map=json_decode(Io_File::valueOf($path)->getContent(), true);

        Cache::set(self::CACHE_KEY.'_map', $map);
      }

      self::$m_unicode=&$map['unicode'];
      self::$m_transformLatn=&$map['transform']['latin'];
    }
    //--------------------------------------------------------------------------
  }
?>
