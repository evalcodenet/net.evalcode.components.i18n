<?php


namespace Components;


  /**
   * I18n_Scriptlet_Import_Cldr_Script
   *
   * @package net.evalcode.components.i18n
   * @subpackage scriptlet.import.cldr
   *
   * @author evalcode.net
   *
   * @todo Replace by service & CLI script / REST resource..
   */
  class I18n_Scriptlet_Import_Cldr_Script extends Http_Scriptlet
  {
    // OVERRIDES
    public function get()
    {
      foreach(self::$m_scripts as $script=>$transformations)
        $this->importScript($script);
    }

    private function importScript($script_)
    {
      $target=Environment::pathComponentsResource('i18n', 'resource', 'i18n', 'script', "$script_.json");

      $ranges=array_chunk(self::$m_unicodeRanges[$script_], 2);

      $range=[];
      foreach($ranges as $r)
        $range=array_merge($range, range(reset($r), end($r)));

      $range=array_flip($range);

      $mapScript=[];

      $mapScriptUnicode=&$mapScript['unicode'];
      $mapScriptTransformations=&$mapScript['transform'];

      foreach(self::$m_scripts[$script_] as $transformation)
      {
        $mapScriptTransformations[$transformation]=[];
        $mapScriptTransformationsCurrent=&$mapScriptTransformations[$transformation];

        $source=self::$m_sources[$script_][$transformation];
        $source=Environment::pathComponentsResource('i18n', 'resource', 'cldr', $source);

        // TODO Implement Io_File_Xml
        $xml=new \SimpleXMLElement(Io_File::valueOf($source)->getContent());

        /* @var $node \SimpleXMLElement */
        foreach($xml->xpath('//supplementalData/transforms/transform/tRule') as $node)
        {
          $string=(string)$node;

          $chars='';
          $trans='';

          if(91===ord($string[0]))
          {
            $chars=mb_substr($string, 1, mb_strpos($string, chr(93))-1);

            $ldim=mb_strrpos($string, chr(226));
            $trans=mb_substr($string, $ldim+1, mb_strrpos($string, chr(59))-$ldim-1);
          }

          $len=mb_strlen($chars);

          for($i=0; $i<$len; $i++)
          {
            $char=mb_substr($chars, $i, 1);
            $dec=Character::unicodeDecimal($char);

            if(isset($range[$dec]))
            {
              $mapScriptUnicode[$dec]="    \"$dec\": \"$char\"";
              $mapScriptTransformationsCurrent[$dec]="      \"$dec\": \"$trans\"";
            }
          }
        }

        $string=implode(",\n", $mapScriptUnicode);
      }

      $transform=[];;
      foreach($mapScript['transform'] as $script=>$transformations)
      {
        $transform[]=sprintf('    "%1$s":%3$s    {%3$s%2$s%3$s    }%3$s',
          $script,
          implode(",\n", $transformations),
          Io::LINE_SEPARATOR_DEFAULT
        );
      }

      $file=new Io_File($target, Io_File::CREATE|Io_File::WRITE|Io_File::TRUNCATE);
      if(false===$file->exists())
        $file->create();

      $file->open();

      // XXX json_encode converts to unicode - which we dont want here - yet maybe implement an alternative Object_Marshaller_Json or Io_File_Json...
      $file->writeLine('{');
      $file->writeLine('  "unicode":');
      $file->writeLine('  {');
      $file->writeLine(implode(",\n", $mapScript['unicode']));
      $file->writeLine('  },');
      $file->writeLine('  "transform":');
      $file->writeLine('  {');
      $file->write(implode(",\n", $transform));
      $file->writeLine('  }');
      $file->writeLine('}');
      $file->close();
    }

    public function post()
    {
      return $this->get();
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_scripts=array(
      'han'=>array(
        'latin'
      )
    );

    private static $m_sources=array(
      'han'=>array(
        'latin'=>'common/transforms/Han-Latin.xml'
      )
    );

    private static $m_unicodeRanges=array(
      'han'=>array(
        /**
         * @see \Components\I18n_Script_Hans::$unicodeRange \Components\I18n_Script_Hans::$unicodeRange
         */
        0x4e00, 0x9fff
      )
    );
    //--------------------------------------------------------------------------
  }
?>
