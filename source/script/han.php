<?php


namespace Components;


  /**
   * I18n_Script_Han
   *
   * @package net.evalcode.components
   * @subpackage i18n.script
   *
   * @author evalcode.net
   */
  class I18n_Script_Han
  {
    // PREDEFINED PROPERTIES
    const CACHE_KEY='i18n/script/han';
    //--------------------------------------------------------------------------


    // PROPERTIES
    public static $unicodeRange=array(
      0x4e00, 0x9fff
      // XXX Evaluate if following are required when actual issues come up.
      // 0x3400, 0x4dbf,
      // 0x20000, 0x2a6df,
      // 0x2a700, 0x2b73f,
      // 0x2b740, 0x2b81f,
      // 0x9f00, 0xfaff,
      // 0x2f800, 0x2fa1f
    );
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * Translates given string of simplified chinese characters
     * into latin ie. pinyin.
     *
     * @param string $string_
     *
     * @return string
     */
    public static function toLatin($string_)
    {
      if(null===self::$m_unicode)
        static::load();

      $transformed='';
      $length=mb_strlen($string_);

      for($i=0; $i<$length; $i++)
      {
        $char=mb_substr($string_, $i, 1);
        $dec=Character::unicodeDecimal($char);

        if(isset(self::$m_transformLatin[$dec]))
          $transformed.=self::$m_transformLatin[$dec].' ';
        else
          $transformed.=$char;
      }

      return rtrim($transformed, ' ');
    }

    public static function contains($string_)
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
    private static $m_transformLatin;
    //-----


    // Terribly slow ... split map into chunks and load ranges on demand or generate php source to include here ..
    private static function load()
    {
      if($map=Cache::get(self::CACHE_KEY.'/map'))
      {
        $path=Environment::pathComponentResource(
          'i18n', 'resource', 'i18n', 'script', 'han.json'
        );

        $map=json_decode(Io_File::valueOf($path)->getContent(), true);

        Cache::set(self::CACHE_KEY.'/map', $map);
      }

      self::$m_unicode=&$map['unicode'];
      self::$m_transformLatin=&$map['transform']['latin'];
    }
    //--------------------------------------------------------------------------
  }
?>
