<?php


namespace Components;


  if(Environment::isDev())
  {
    I18n_Scriptlet_Import_Cldr_Common::serve('import/cldr/common');
    I18n_Scriptlet_Import_Cldr_Script::serve('import/cldr/script');
  }
?>
