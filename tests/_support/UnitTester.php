<?php
namespace pawcode\cart\tests;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
class UnitTester extends \Codeception\Actor
{
    use _generated\UnitTesterActions;

   /**
    * Define custom actions here
    */

    public function invokeMethod($object, $methodName, $args = [])
    {
        $reflector = new \ReflectionClass($object);
        $method = $reflector->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $args);
    }

    public function invokeProperty($object, $propertyName, $value = '____________________the_value_that_not_prossible_match_')
    {
        $reflector = new \ReflectionClass($object);
        $property = $reflector->getProperty($propertyName);
        $property->setAccessible(true);
        if ($value !== '____________________the_value_that_not_prossible_match_') {
            $property->setValue($object, $value);
        }
        return $property->getValue($object);
    }
}
