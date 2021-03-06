<?php
/**
 * @version $Id$
 */

/**
 * @see AbstractTest
 */
require_once 'src/test/AbstractTest.php';

class phpRack_Package_Php_PearTest extends AbstractTest
{
    /**
     * @var phpRack_Package_Php_Pear
     */
    private $_package;

    protected function setUp()
    {
        parent::setUp();
        $this->_package = $this->_test->assert->php->pear;
    }

    public function testPackage()
    {
        $this->_package->package('PEAR');
    }

    public function testPackageWithNotExistingPearPackage()
    {
        $this->_package->package('NotExistingPearPackage');
    }

    public function testAtLeast()
    {
        $this->_package->package('PEAR')
            ->atLeast('1.0');
    }

    public function testAtLeastWithVeryHighVersion()
    {
        $this->_package->package('PEAR')
            ->atLeast('999.0');
    }

    public function testAtLeastWithoutPackage()
    {
        $this->_package->atLeast('1.0');
    }

    public function testShowList()
    {
        $this->_package->showList();
    }
}
