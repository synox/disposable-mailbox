<?php
/*
 * This file is part of the PronounceableWord library.
 *
 * (c) Loic Chardonnet
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__) . '/../../../src/PronounceableWord/DependencyInjectionContainer.php';

class PronounceableWord_Tests_DependencyInjectionContainerTest extends PHPUnit_Framework_TestCase {
    public function testClassNames() {
        $container = new PronounceableWord_DependencyInjectionContainer();

        foreach ($container->classNames as $classType => $className) {
            $expectedClassName = 'PronounceableWord_' . $classType;
            $this->assertSame($expectedClassName, $className);
        }
    }


    public function testClassNamesAndInstances() {
        $container = new PronounceableWord_DependencyInjectionContainer();

        foreach ($container->classNames as $classType => $className) {
            $getMethodName = 'get' . $classType;
            $instance = $container->{$getMethodName}();

            $this->assertInstanceOf($className, $instance);
        }
    }

    public function testConfigurations() {
        $container = new PronounceableWord_DependencyInjectionContainer();

        foreach ($container->configurations as $className => $configurationInstance) {
            $configurationClassName = 'PronounceableWord_Configuration_' . $className;

            $this->assertInstanceOf($configurationClassName, $configurationInstance);
        }
    }
}
