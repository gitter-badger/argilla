<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
class ArrTest extends CTestCase
{
  public function testGet()
  {
    $array = [1 => 2, 3 => ''];

    $result = Arr::get($array, 1);
    $this->assertEquals(2, $result);

    $result = Arr::get($array, 3, 33, true);
    $this->assertEquals(33, $result);
  }

  public function testCut()
  {
    $array = [1 => 2, 3 => ''];

    $result = Arr::cut($array, 1);
    $this->assertEquals(2, $result);
    $this->assertNotContains(1, $array);

    $result = Arr::cut($array, 3, 33, true);
    $this->assertEquals(33, $result);
    $this->assertNotContains(33, $array);
  }

  public function testExtract()
  {
    $array = [1 => 2, 3 => 4, 5 => 6];

    $result = Arr::extract($array, 1);
    $this->assertEquals(2, $result);

    $result = Arr::extract($array, [1]);
    $this->assertEquals([1 => 2], $result);
  }

  public function testReset()
  {
    $array = [1 => 2, 3 => 4, 5 => 6];

    $result = Arr::reset($array);
    $this->assertEquals(2, $result);
  }

  public function testEnd()
  {
    $array = [1 => 2, 3 => 4, 5 => 6];

    $result = Arr::end($array);
    $this->assertEquals(6, $result);
  }

  public function testKeysExists()
  {
    $array = [1 => 2, 3 => 4, 5 => 6];

    $result = Arr::keysExists($array, 1);
    $this->assertTrue($result);

    $result = Arr::keysExists($array, [1]);
    $this->assertTrue($result);

    $result = Arr::keysExists($array, [7]);
    $this->assertFalse($result);
  }

  public function testFromObj()
  {
    $array = [1 => 2, 3 => 4, 5 => 6];

    $object = new stdClass();
    $object->a = 1;

    $result = Arr::fromObj($object);
    $this->assertEquals(['a' => 1], $result);

    $result = Arr::fromObj($array);
    $this->assertEquals($array, $result);
  }

  public function testIsIntersec()
  {
    $array = [1 => 2, 3 => 4, 5 => 6];

    $result = Arr::isIntersec($array, [1 => 2]);
    $this->assertTrue($result);

    $result = Arr::isIntersec($array, [1 => 'a']);
    $this->assertFalse($result);
  }

  public function testTrim()
  {
    $array = [' a', 'b| '];

    $result = Arr::trim($array);
    $this->assertEquals(['a', 'b|'], $result);

    $result = Arr::trim($array, ' |');
    $this->assertEquals(['a', 'b'], $result);
  }

  public function testImplode()
  {
    $array = ['a', ' b', '', ' c'];

    $result = Arr::implode($array, ", ");
    $this->assertEquals('a, b, c', $result);
  }

  public function testReflect()
  {
    $array = [1, 2];

    $result = Arr::reflect($array);
    $this->assertEquals([1 => 1, 2 => 2], $result);
  }

  public function testReduce()
  {
    $result = Arr::reduce([1, 2]);
    $this->assertEquals(1, $result);

    $result = Arr::reduce(3);
    $this->assertEquals(3, $result);
  }

  public function testPush()
  {
    $array = [1 => [1]];

    Arr::push($array, 1, 2);
    Arr::push($array, 2, 1);
    $this->assertEquals([1 => [1, 2], 2 => [1]], $array);
  }

  public function testDivideEmpty()
  {
    $this->assertEquals(array(), Arr::divide(array()));
  }

  public function testDivideOneColumn()
  {
    $array = array('one', 'two');

    $result = Arr::divide($array, 1);
    $this->assertEquals(array(array('one', 'two')), $result);
  }

  public function testDivideTwoColumn()
  {
    $array = array('one');
    $result = Arr::divide($array, 2);
    $this->assertEquals(array(array('one')), $result);

    $array = array('one', 'two');
    $result = Arr::divide($array, 2);
    $this->assertEquals(array(array('one'), array('two')), $result);

    $array = array('one', 'two', 'three', 'four', 'five');
    $result = Arr::divide($array, 2);
    $this->assertEquals(array(array('one', 'two', 'three'), array('four', 'five')), $result);
  }

  public function testDivideThreeColumn()
  {
    $array = array('one');
    $result = Arr::divide($array, 3);
    $this->assertEquals(array(array('one')), $result);

    $array = array('one', 'two');
    $result = Arr::divide($array, 3);
    $this->assertEquals(array(array('one'), array('two')), $result);

    $array = array('one', 'two', 'three', 'four', 'five');
    $result = Arr::divide($array, 3);
    $this->assertEquals(array(array('one', 'two'), array('three', 'four'), array('five')), $result);

    $array = array('one', 'two', 'three', 'four', 'five', 'six');
    $result = Arr::divide($array, 3);
    $this->assertEquals(array(array('one', 'two'), array('three', 'four'), array('five', 'six')), $result);
  }

  public function testDivideFourColumn()
  {
    $array = array('one');
    $result = Arr::divide($array, 4);
    $this->assertEquals(array(array('one')), $result);

    $array = array('one', 'two');
    $result = Arr::divide($array, 4);
    $this->assertEquals(array(array('one'), array('two')), $result);

    $array = array('one', 'two', 'three', 'four', 'five');
    $result = Arr::divide($array, 4);
    $this->assertEquals(array(array('one', 'two'), array('three'), array('four'), array('five')), $result);

    $array = array('one', 'two', 'three', 'four', 'five', 'six');
    $result = Arr::divide($array, 4);
    $this->assertEquals(array(array('one', 'two'), array('three', 'four'), array('five'), array('six')), $result);
  }

  public function testDivideAssociationIndex()
  {
    $array = array('oneIndex' => 'one', 'two' => 'two', 'three' => 'three', 'four' => 'four', 'five' => 'five', 'six' => 'six');
    $result = Arr::divide($array, 4, false);
    $this->assertEquals(array(array('oneIndex' => 'one', 'two' => 'two'), array('three' => 'three', 'four' => 'four'), array('five' => 'five'), array('six' => 'six')), $result);
  }

  public function testDivideFlipSort()
  {
    $array = array( 'one', 'two', 'three', 'four', 'five', 'six');
    $result = Arr::divide($array, 4, true, true);
    $this->assertEquals(array(array('one', 'five'), array('two', 'six'), array('three'), array('four')), $result);
  }

  public function testArrayMergeAssoc()
  {
    $array1 = [1 => 'a'];
    $array2 = [2 => 'b'];

    $result = Arr::mergeAssoc($array1, $array2);
    $this->assertEquals([1 => 'a', 2 => 'b'], $result);

    // test key overriding
    $array1 = [1 => 'a', 2 => 'b'];
    $array2 = [2 => 'c'];

    $result = Arr::mergeAssoc($array1, $array2);
    $this->assertEquals([1 => 'a', 2 => 'c'], $result);
  }

  public function testInsertAfter()
  {
    $array = ['a' => 'aa', 'b' => 'bb'];
    Arr::insertAfter($array, 'c', 'cc', 'b');
    $this->assertEquals(['a' => 'aa', 'b' => 'bb', 'c' => 'cc'], $array);

    $array = [1 => 'a', 3 => 'c'];
    Arr::insertAfter($array, 2, 'b', 1);
    $this->assertEquals([1 => 'a', 2 => 'b', 3 => 'c'], $array);
  }
}