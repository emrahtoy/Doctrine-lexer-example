<?php

require_once __DIR__ . '/../vendor/autoload.php';
/* 
  *  Emrah TOY .
  *  http://www.emrahtoy.com
  *  code@emrahtoy.com
 */



$input="name,user_id,photo.fields(photo_id,url,tagged_people.fields(name,people_id,city.fields(city_id,name)).limit(20)).limit(5).offset(2)";

$parser=new Emr\Common\QueryParser($input,$filter);
$test=$parser->parse();

echo "<pre>".print_r($test,true)."</pre>";