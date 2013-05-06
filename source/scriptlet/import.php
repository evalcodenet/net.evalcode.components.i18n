<?php


namespace Components;


  /**
   * I18n_Scriptlet_Import
   *
   * @package net.evalcode.components
   * @subpackage i18n.scriptlet
   *
   * @author evalcode.net
   *
   * @todo Replace by CLI script ..
   */
  class I18n_Scriptlet_Import extends Http_Scriptlet
  {
    // OVERRIDES
    public function post()
    {
      $locale=$_REQUEST['locale'];

      $source=dirname(dirname(dirname(__DIR__)))."/resource/cldr/common/main/$locale.xml";
      $target=dirname(dirname(dirname(__DIR__)))."/resource/i18n/common/$locale.xml";

      $document=new \DOMDocument('1.0', 'utf-8');
      $document->standalone=true;
      $document->formatOutput=true;
      $document->preserveWhiteSpace=true;

      $xml=new \SimpleXMLElement(file_get_contents($source));

      $values=array();
      foreach($xml->localeDisplayNames->languages->language as $node)
      {
        if(2===strlen($code=(string)$node['type']))
          $values[(string)$node['type']]=(string)$node;
      }
      asort($values);
      $this->append($document, 'common/language', $values);

      $values=array();
      foreach($xml->localeDisplayNames->territories->territory as $node)
      {
        if(2===strlen($code=(string)$node['type']))
          $values[$code]=(string)$node;
      }
      asort($values);
      $this->append($document, 'common/country', $values);

      $values=array();
      foreach($xml->localeDisplayNames->scripts->script as $node)
        $values[(string)$node['type']]=(string)$node;
      asort($values);
      $this->append($document, 'common/script', $values);

      $values=array();
      foreach($xml->numbers->currencies->currency as $node)
        $values[(string)$node['type']]=(string)$node->displayName[0];
      asort($values);
      $this->append($document, 'common/currency', $values);

      $values=array();
      foreach($xml->xpath('//dates/calendars/calendar[@type="gregorian"]/months/monthContext[@type="format"]/monthWidth[@type="abbreviated"]/month') as $node)
        $values[(string)$node['type']]=(string)$node;
      $this->append($document, 'common/date/month/long', $values, self::$m_months);

      $values=array();
      foreach($xml->xpath('//dates/calendars/calendar[@type="gregorian"]/months/monthContext[@type="format"]/monthWidth[@type="wide"]/month') as $node)
        $values[(string)$node['type']]=(string)$node;
      $this->append($document, 'common/date/month/short', $values, self::$m_months);

      $values=array();
      foreach($xml->xpath('//dates/calendars/calendar[@type="gregorian"]/days/dayContext[@type="format"]/dayWidth[@type="short"]/day') as $node)
        $values[(string)$node['type']]=(string)$node;
      $this->append($document, 'common/date/day/short', $values, self::$m_days);

      $values=array();
      foreach($xml->xpath('//dates/calendars/calendar[@type="gregorian"]/days/dayContext[@type="format"]/dayWidth[@type="abbreviated"]/day') as $node)
        $values[(string)$node['type']]=(string)$node;
      $this->append($document, 'common/date/day/abbreviated', $values, self::$m_days);

      $values=array();
      foreach($xml->xpath('//dates/calendars/calendar[@type="gregorian"]/days/dayContext[@type="format"]/dayWidth[@type="wide"]/day') as $node)
        $values[(string)$node['type']]=(string)$node;
      $this->append($document, 'common/date/day/long', $values, self::$m_days);

      $values=array();
      foreach($xml->xpath('//dates/calendars/calendar[@type="gregorian"]/dateFormats/*') as $node)
        $values[(string)$node['type']]=str_replace(array('EEEE', 'dd', 'MMMM', 'MMM', 'MM', 'M', 'yyyy', 'yy', 'y'), array('l', 'd', 'F', 'M', 'm', 'm', 'Y', 'y', 'Y'), (string)$node->dateFormat->pattern);
      $this->append($document, 'common/date/pattern', $values);

      $values=array();
      foreach($xml->xpath('//dates/calendars/calendar[@type="gregorian"]/timeFormats/*') as $node)
        $values[(string)$node['type']]=str_replace(array('HH', 'mm', 'ss', 'zzzz', 'z'), array('H', 'i', 's', 'O', 'T'), (string)$node->timeFormat->pattern);
      $this->append($document, 'common/time/pattern', $values);

      @file_put_contents($target, $document->saveXML());
    }

    public function get()
    {
      return $this->post();
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_days=array(
      1=>'mon',
      2=>'tue',
      3=>'wed',
      4=>'thu',
      5=>'fri',
      6=>'sat',
      7=>'sun',
    );
    private static $m_months=array(
      1=>'jan',
      2=>'feb',
      3=>'mar',
      4=>'apr',
      5=>'may',
      6=>'jun',
      7=>'jul',
      8=>'aug',
      9=>'sep',
      10=>'oct',
      11=>'nov',
      12=>'dec'
    );
    //-----


    private function append(\DOMDocument $document_, $path_, array $values_, array $map_=array())
    {
      $tags=explode('/', $path_);

      $elements=array(
        -1=>$document_
      );

      for($i=0, $j=count($tags); $i<$j; $i++)
      {
        $tag=$tags[$i];
        $existing=$elements[$i-1]->getElementsByTagName($tags[$i]);

        if(1===$existing->length)
          $elements[$i]=$existing->item(0);
        else
          $elements[$i]=$document_->createElement(strtolower(trim($tags[$i])));

        $elements[$i-1]->appendChild($elements[$i]);

        if($i===($j-1))
        {
          foreach($values_ as $key=>$value)
          {
            if(isset($map_[$key]))
              $key=$map_[$key];

            $elements[$i]->appendChild($document_->createElement(strtolower(trim($key)), $value));
          }
        }
      }
    }
    //--------------------------------------------------------------------------
  }
?>
