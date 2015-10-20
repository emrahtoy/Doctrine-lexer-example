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
        $input="name,user_id,photo.fields(photo_id,url,tagged_people.fields(name,people_id,city.fields(city_id,name)).limit(20)).limit(5).offset(2)";
        $this->parser = new \Emr\Common\QueryParser($input);
    }

    /**
     * @todo write tests
     */
    public function testParser()
    {
        echo "<pre>".print_r($this->parser->parse(),true)."</pre>";
    }
}