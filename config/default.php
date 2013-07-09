<?php


namespace Components;


  if(Environment::isDev())
  {
    I18n_Scriptlet_Import_Cldr_Common::serve('import/cldr/common');
    I18n_Scriptlet_Import_Cldr_Script::serve('import/cldr/script');

    I18n_Scriptlet_Import_Geo_Cities::serve('import/geo/cities');
    I18n_Scriptlet_Import_Geo_Regions::serve('import/geo/regions');
    I18n_Scriptlet_Import_Geo_Regions_Translations::serve('import/geo/regions/translations');
  }
?>
