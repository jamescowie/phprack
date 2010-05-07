<?php
/**
 * phpRack: Integration Testing Framework
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt. It is also available
 * through the world-wide-web at this URL: http://www.phprack.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@phprack.com so we can send you a copy immediately.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @copyright Copyright (c) phpRack.com
 * @version $Id$
 * @category phpRack
 */

/**
 * Parent class of all test suites
 *
 * @package Tests
 */
abstract class PhpRack_Suite
{
    /**
     * Runner of tests
     *
     * @var phpRack_Runner
     * @see __construct()
     * @see _addTest()
     * @see _addSuite()
     */
    private $_runner;

    /**
     * Suite tests
     *
     * @var array of phpRack_Test
     * @see getTests()
     * @see _addTest()
     */
    private $_tests = array();

    /**
     * Sub-suites
     *
     * @var array of PhpRack_Suite
     * @see getTests()
     * @see _addSuite()
     */
    private $_suites = array();

    /**
     * Create new instance of the class, using PHP absolute file name
     *
     * @param string ID of the suite, absolute (!) file name
     * @param phpRack_Runner Instance of test runner
     * @return phpRack_Suite
     * @throws Exception
     * @see _addSuite()
     * @see phpRack_Runner::getTests()
     */
    public static function factory($fileName, $runner)
    {
        if (!file_exists($fileName)) {
            throw new Exception("File '{$fileName}' is not found");
        }

        if (!preg_match(phpRack_Runner::SUITE_PATTERN, $fileName)) {
            throw new Exception("File '{$fileName}' is not named properly, can't run it");
        }

        $className = pathinfo($fileName, PATHINFO_FILENAME);

        // workaround against ZCA static code analysis
        eval('require_once $fileName;');
        return new $className($fileName, $runner);
    }

    /**
     * Get tests defined in this suite and sub suites
     *
     * @return array of phpRack_Test
     * @see phpRack_Runner::getTests()
     */
    public function getTests()
    {
        $tests = $this->_tests;
        foreach ($this->_suites as $suite) {
            $tests = array_merge($tests, $suite->getTests());
        }
        return $tests;
    }

    /**
     * Allow child class to add tests and sub suites by overwritting this
     * method
     *
     * @return void
     * @see __construct()
     */
    protected function __init()
    {

    }

    /**
     * Add suite
     *
     * @param string Suite name
     * @param array options
     * @return $this
     * @throws Exception if suite can't be found
     * @see MySuite::_init()
     * @todo #48 From what locations we should load these suites. We will have
     *           built-in suites and maybe also user-defined?
     */
    protected function _addSuite($suiteName, array $options = array())
    {
        // @see #48
        $suitePath = $suiteName;
        // Exception is possible here
        $this->_suites[] = self::factory($suitePath, $this->_runner, $options);
        return $this;
    }

    /**
     * Add test
     *
     * @param string Suite name
     * @param array options
     * @return $this
     * @throws Exception if test can't be found
     * @see MySuite::_init()
     * @todo #48 From what locations we should load these tests?
     *           "integration-tests" folder or other?
     *           How we should map $testName to $testPath?
     *
     *           How we should pass options to test constructor/factory
     *           and how use this param inside it?
     */
    protected function _addTest($testName, array $options = array())
    {
        // @see #48
        $testPath = $testName;
        // @see #48
        assert(is_array($options)); // for ZCA only
        // Exception is possible here
        $this->_tests[] = phpRack_Test::factory($testPath, $this->_runner);
        return $this;
    }

    /**
     * Test suite constructor
     *
     * @return void
     * @see factory()
     */
    private function __construct()
    {
        $this->_init();
    }
}
