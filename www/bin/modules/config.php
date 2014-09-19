<?php

// CONFIG

G::$modules = array(
  #  ALIAS => ClassName
    "menu" => "MMenu",
	"catalogue" => "MCatalog",
	"images" => "MImages",
	"pages" => "MPages"
);

// PLUG IN

foreach(G::$modules as $module_config){
    include_once($module_config . ".php");
}

?>