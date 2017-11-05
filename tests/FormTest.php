<?php 	

use PHPUnit\Framework\TestCase as TestCase;

/**
 * Test de la classe Form
 */
class FormTest extends TestCase
{
    public function testIsPassword()
    {
    	$this->assertEquals("s#Am6je8", App\Form::isPassword("s#Am6je8"));
    }

    public function testIsNotPassword()
    {
    	$this->expectException(\Exception::class);
    	App\Form::isPassword("s#Amje");
    }

    public function testIsInt()
    {
    	$this->assertEquals(1, App\Form::isInt("1"));
    }

    public function testIsNotInt()
    {
    	$this->expectException(\Exception::class);
    	App\Form::isInt("Oxford");
    }

    public function testIsStringWithLess5Caracters() {
    	$this->assertEquals("Oxford", App\Form::isString("Oxford", 2));
    }

    public function testIsStringWithMore5Caracters() {
    	$this->assertEquals("Oxford", App\Form::isString("Oxford"));
    }

    public function testIsNotString() {
    	$this->expectException(\Exception::class);
    	$this->assertEquals("Oxford", App\Form::isString(5));
    }

    public function testIsNotStringWith5Caracters() {
    	$this->expectException(\Exception::class);
    	App\Form::isString("SIO");
    }

    public function testIsMailAddress() {
    	$this->assertEquals('btssio@gmail.com', App\Form::isMail('btssio@gmail.com'));
    }

    public function testIsNotMailAddress() {
    	$this->expectException(\Exception::class);
    	App\Form::isMail("btssiogmail.com");
    }

    public function testIsNotDate()
    {
    	$this->expectException(\Exception::class);
    	App\Form::isDate('0511/2017');
    }

    public function testIsValidSex()
    {
    	$this->assertEquals('M', App\Form::isSex('M'));
    }

    public function testIsNotValidSex()
    {
    	$this->expectException(\Exception::class);
    	App\Form::isSex('A');
    }

    public function testIsNotEmpty()
    {
    	$array = array('col' => 'row', 'row' => 'col');
    	$fields = array('col', 'row');
    	$this->assertTrue(App\Form::isNotEmpty($array, $fields));
    }

    public function testFieldsAreEmpty()
    {
    	$this->expectException(\Exception::class);
    	$array = array('col' => '', 'row' => 'col');
    	$fields = array('col', 'row');
    	App\Form::isNotEmpty($array, $fields);
    }

    public function testDatasNotProvided()
    {
    	$this->expectException(\Exception::class);
    	$array = array('col' => 'row');
    	$fields = array('col', 'row');
    	App\Form::isNotEmpty($array, $fields);
    }
}