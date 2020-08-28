<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
namespace ZendTest\Validator;

use DateTime;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Zend\Validator;
use Zend\Validator\Exception\InvalidArgumentException;

/**
 * @covers     Zend\Validator\IsInstanceOf
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class IsInstanceOfTest extends TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $validator = new Validator\IsInstanceOf(DateTime::class);
        $this->assertTrue($validator->isValid(new DateTime())); // True
        $this->assertFalse($validator->isValid(null)); // False
        $this->assertFalse($validator->isValid($this)); // False

        $validator = new Validator\IsInstanceOf(\Exception::class);
        $this->assertTrue($validator->isValid(new \Exception())); // True
        $this->assertFalse($validator->isValid(null)); // False
        $this->assertFalse($validator->isValid($this)); // False

        $validator = new Validator\IsInstanceOf(TestCase::class);
        $this->assertTrue($validator->isValid($this)); // True
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $validator = new Validator\IsInstanceOf(DateTime::class);
        $this->assertEquals([], $validator->getMessages());
    }

    /**
     * Ensures that getClassName() returns expected value
     *
     * @return void
     */
    public function testGetClassName()
    {
        $validator = new Validator\IsInstanceOf(DateTime::class);
        $this->assertEquals(DateTime::class, $validator->getClassName());
    }

    public function testEqualsMessageTemplates()
    {
        $validator  = new Validator\IsInstanceOf(DateTime::class);
        $reflection = new ReflectionClass($validator);

        $property = $reflection->getProperty('messageTemplates');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($validator),
            $validator->getOption('messageTemplates')
        );
    }

    public function testEqualsMessageVariables()
    {
        $validator  = new Validator\IsInstanceOf(DateTime::class);
        $reflection = new ReflectionClass($validator);

        $property = $reflection->getProperty('messageVariables');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($validator),
            $validator->getOption('messageVariables')
        );
    }

    public function testPassTraversableToConstructor()
    {
        $validator = new Validator\IsInstanceOf(new \ArrayIterator(['className' => DateTime::class]));
        $this->assertEquals(DateTime::class, $validator->getClassName());
        $this->assertTrue($validator->isValid(new DateTime()));
        $this->assertFalse($validator->isValid(null));
        $this->assertFalse($validator->isValid($this));
    }

    public function testPassOptionsWithoutClassNameKey()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing option "className"');

        $options   = ['NotClassNameKey' => DateTime::class];
        $validator = new Validator\IsInstanceOf($options);
    }
}