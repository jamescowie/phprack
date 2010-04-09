<?php
/**
 * @version $Id$
 */

/**
 * @see phpRack_Test
 */
require_once PHPRACK_PATH . '/Test.php';

class PearTest extends phpRack_Test
{
    public function testPearPackages()
    {
        $this->assert->php->pear
            ->package('HTTP_Client') // package exists
            ->atLeast('1.2.1'); // at least this version is present
    }
}