<?php

namespace ENC\Bundle\BackupRestoreBundle\Tests\Restore;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use ENC\Bundle\BackupRestoreBundle\Restore;

class TestRestoreFactory extends WebTestCase
{
    public static function getMock($platform, array $addMethods = null, array $onlyMethods = null, array $constructorArguments = []): MockObject
    {
        $instance = new self();
        
        switch ($platform) {
            case 'mysql':
                $constructorArguments = empty($constructorArguments) ? array(self::getDbalConnectionMock()) : $constructorArguments;

                $mockBuilder = $instance
                    ->getMockBuilder(Restore\MySql\MySqlRestore::class)
                    ->setConstructorArgs($constructorArguments);

                if ($addMethods) {
                    $mockBuilder->addMethods($addMethods);
                }

                if ($onlyMethods) {
                    $mockBuilder->onlyMethods($onlyMethods);
                }

                return $mockBuilder->getMock();
            default:
                throw new \InvalidArgumentException(sprintf('"%s" is not a valid database platform or is not supported by this bundle.', $platform));
                
                break;
        }
    }
    
    public static function getDbalConnectionMock(array $methods = array()): MockObject
    {
        $instance = new self();

        return $instance
            ->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->onlyMethods($methods)
            ->getMock();
    }
}
