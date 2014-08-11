<?php

namespace Lmc\Steward\Test;

/**
 * Listener to initialize and destroy WebDriver befaore and after each tests.
 *
 * Note: This is done as listener rather then in setUp() and tearDown(), as a workaround
 * for the sequence in which PHPUnit executes tearDown() of tests and addFailure() on listeners.
 * If taking screenshot using addFailure(), tearDown() would have already been called and the
 * browser would be closed.
 */
class WebdriverListener extends \PHPUnit_Framework_BaseTestListener
{
    public function startTest(\PHPUnit_Framework_Test $test)
    {
        if (!$test instanceof AbstractTestCase) {
            throw new \InvalidArgumentException('Test case must be descendant of Lmc\Steward\Test\AbstractTestCase');
        }

        $test->log('Initializing "%s" webdriver for "%s::%s"', BROWSER_NAME, get_class($test), $test->getName());

        $capabilities = [
            \WebDriverCapabilityType::BROWSER_NAME => BROWSER_NAME,
        ];

        if (BROWSER_NAME == 'internet explorer') {
            // When set to true, this capability clears the cache, cookies, history, and saved form data.
            $capabilities['ie.ensureCleanSession'] = true;
        }

        $test->wd = RemoteWebDriver::create(SERVER_URL .  '/wd/hub', $capabilities, $timeoutInMs = 2*60*1000);

        // Force screen resolution for Phantom, otherwise it uses some weird unchangeable defaults
        if (BROWSER_NAME == 'phantomjs') {
            $test->wd->manage()->window()->setSize(new \WebDriverDimension(1280, 1024));
        }
    }

    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        if (!$test instanceof AbstractTestCase) {
            throw new \InvalidArgumentException('Test case must be descendant of Lmc\Steward\Test\AbstractTestCase');
        }

        if ($test->wd instanceof \RemoteWebDriver) {
            $test->log(
                'Destroying "%s" webdriver for "%s::%s" (session %s)',
                BROWSER_NAME,
                get_class($test),
                $test->getName(),
                $test->wd->getSessionID()
            );

            // Necessary for phantomjs - see https://github.com/detro/ghostdriver/issues/343
            $test->wd->getCommandExecutor()->execute('deleteAllCookies');

            $test->wd->close();
            $test->wd->quit();
        }
    }
}