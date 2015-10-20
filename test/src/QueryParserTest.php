<?php
/**
 * Created by PhpStorm.
 * User: Zeki
 * Date: 20.10.2015
 * Time: 16:32
 */
class QueryParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Emr\Common\QueryParser
     */
    protected $parser;

    public function setUp()
    {
        $input="name,user_id,photo.fields(photo_id,url,tagged_people.fields(name,people_id,city.fields(city_id,name).order_by(name).ASC.limit(20)).limit(5).offset(2)                                                                                                                                     .DESC";
        $this->parser = new \Emr\Common\QueryParser($input);

        $parser=new Emr\Common\QueryParser($input);

        echo "<pre>".print_r($this->parser->parse(),true)."</pre>";
    }
}