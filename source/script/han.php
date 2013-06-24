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
      if(false===static::contains($string_))
        return $string_;

      $transformed=array();
      foreach(String::split($string_) as $char)
      {
        $idx=mb_strpos(self::$m_index, $char);
        if(isset(self::$m_map[$idx]))
          $transformed[]=self::$m_map[$idx];
      }

      return implode(' ', $transformed);
    }

    public static function contains($string_)
    {
      if(null===self::$m_index)
        static::load();

      $length=mb_strlen($string_);

      // Can't be chinese (multi-byte) if strlen delivers correct/same result as mb_strlen.
      if($length===strlen($string_))
        return false;

      for($i=0; $i<$length; $i++)
      {
        if(false!==mb_strpos(self::$m_index, mb_substr($string_, $i, 1)))
          return true;
      }

      // TODO Optimize (at least the negative case) ..
      return false;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_map=array();
    private static $m_index;
    //-----


    private static function load()
    {
      if(false===(self::$m_index=Cache::get(self::CACHE_KEY.'/index'))
        || false===(self::$m_map=Cache::get(self::CACHE_KEY.'/map')))
      {
        $pathXml=Environment::pathComponentResource(
          'i18n', 'resource', 'cldr', 'common', 'transforms', 'Han-Latin.xml'
        );

        $xml=new \SimpleXMLElement(Io_File::valueOf($pathXml)->getContent());

        self::$m_map=array();
        self::$m_index='';

        /* @var $node \SimpleXMLElement */
        foreach($xml->xpath('//supplementalData/transforms/transform/tRule') as $node)
        {
          $value=(string)$node;

          $index='';
          $pinyin='';

          if(91===ord($value[0]))
          {
            $index=mb_substr($value, 1, mb_strpos($value, chr(93))-1);

            $ldim=mb_strrpos($value, chr(226));
            $pinyin=mb_substr($value, $ldim+1, mb_strrpos($value, chr(59))-$ldim-1);
          }

          $start=mb_strlen(self::$m_index);
          $end=$start+mb_strlen($index);

          self::$m_index.=$index;
          for($i=$start; $i<$end; $i++)
            self::$m_map[$i]=$pinyin;
        }

        Cache::set(self::CACHE_KEY.'/index', self::$m_index);
        Cache::set(self::CACHE_KEY.'/map', self::$m_map);
      }
    }
    //--------------------------------------------------------------------------
  }
?>
