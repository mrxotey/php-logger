<?php

class LoggerConfiguratorTest extends PHPUnit_Framework_TestCase
{
    public function testConfigure()
    {
        $hierarchy = new LoggerHierarchy();
        $configurator = new LoggerConfigurator();
        $configurator->configure($hierarchy, array(
            'layouts' => array(
                'simple' => array(
                    'class' => 'LoggerLayoutSimple',
                ),
                'pattern' => array(
                    'class' => 'LoggerLayoutPattern',
                    'pattern' => '{date:Y/m/d} [{level}] {logger} {file}:{line} {class}:{function} {mdc:key} {mdc} {ndc}: {message} {ex}',
                ),
            ),
            'appenders' => array(
                'stream' => array(
                    'class' => 'LoggerAppenderStream',
                    'stream' => 'php://stdout',
                    'useLock' => true,
                    'useLockShortMessage' => false,
                    'minLevel' => 0,
                    'maxLevel' => PHP_INT_MAX,
                    'layout' => 'simple',
                ),
            ),
            'loggers' => array(
                'logger' => array(
                    'appenders' => array('stream', array(
                        'class' => 'LoggerAppenderStream',
                        'stream' => 'php://stdout',
                        'useLock' => true,
                        'useLockShortMessage' => false,
                        'minLevel' => 0,
                        'maxLevel' => PHP_INT_MAX,
                        'layout' => 'simple'
                    )),
                    'addictive' => false,
                ),
            ),
            'root' => array(
                'appenders' => array('stream'),
            )
        ));
        $this->assertArrayHasKey('simple', $hierarchy->getLayoutMap());
        $this->assertArrayHasKey('pattern', $hierarchy->getLayoutMap());
        $this->assertArrayHasKey('stream', $hierarchy->getAppenderMap());
        $this->assertArrayHasKey('logger', $hierarchy->getLoggerMap());
    }

    public function testInvalidLayout()
    {
        $this->setExpectedException('LoggerException');
        $hierarchy = new LoggerHierarchy();
        $configurator = new LoggerConfigurator();
        $configurator->configure($hierarchy, array(
            'layouts' => array(
                'simple' => array(),
            )
        ));
    }

    public function testInvalidLayoutInAppenderEmpty()
    {
        $this->setExpectedException('LoggerException');
        $hierarchy = new LoggerHierarchy();
        $configurator = new LoggerConfigurator();
        $configurator->configure($hierarchy, array(
            'appenders' => array(
                'stream' => array(
                    'class' => 'LoggerAppenderStream',
                    'stream' => 'php://stdout',
                    'useLock' => true,
                    'useLockShortMessage' => false,
                    'minLevel' => 0,
                    'maxLevel' => PHP_INT_MAX,
                    'layout' => array(),
                ),
            ),
        ));
    }

    public function testInvalidLayoutInAppenderInvalidType()
    {
        $this->setExpectedException('LoggerException');
        $hierarchy = new LoggerHierarchy();
        $configurator = new LoggerConfigurator();
        $configurator->configure($hierarchy, array(
            'appenders' => array(
                'stream' => array(
                    'class' => 'LoggerAppenderStream',
                    'stream' => 'php://stdout',
                    'useLock' => true,
                    'useLockShortMessage' => false,
                    'minLevel' => 0,
                    'maxLevel' => PHP_INT_MAX,
                    'layout' => new stdClass(),
                ),
            ),
        ));
    }

    public function testInvalidAppender()
    {
        $this->setExpectedException('LoggerException');
        $hierarchy = new LoggerHierarchy();
        $configurator = new LoggerConfigurator();
        $configurator->configure($hierarchy, array(
            'appenders' => array(
                'stream' => array(
                    'stream' => 'php://stdout',
                    'useLock' => true,
                    'useLockShortMessage' => false,
                    'minLevel' => 0,
                    'maxLevel' => PHP_INT_MAX,
                ),
            ),
        ));
    }

    public function testInvalidAppenderInLogger()
    {
        $this->setExpectedException('LoggerException');
        $hierarchy = new LoggerHierarchy();
        $configurator = new LoggerConfigurator();
        $configurator->configure($hierarchy, array(
            'loggers' => array(
                'logger' => array(
                    'appenders' => array(new stdClass()),
                    'addictive' => false,
                ),
            ),
        ));
    }
}