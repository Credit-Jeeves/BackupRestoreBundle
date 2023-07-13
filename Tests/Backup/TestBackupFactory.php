<?php

namespace ENC\Bundle\BackupRestoreBundle\Tests\Backup;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use ENC\Bundle\BackupRestoreBundle\Backup;

class TestBackupFactory extends WebTestCase
{
    public static function getMock($platform, array $methods = [], array $constructorArguments = []): MockObject|Backup\MySql\MySqlBackup|Backup\MongoDB\MongoDBBackup
    {
        $instance = new self();
        
        switch ($platform) {
            case 'mysql':
                $constructorArguments = empty($constructorArguments) ? array(self::getDbalConnectionMock()) : $constructorArguments;
                
                return $instance
                    ->getMockBuilder(Backup\MySql\MySqlBackup::class)
                    ->onlyMethods($methods)
                    ->setConstructorArgs($constructorArguments)
                    ->getMock();
            case 'mongodb':
                $constructorArguments = empty($constructorArguments) ? array(self::getMongoDBConnectionMock()) : $constructorArguments;

                return $instance
                    ->getMockBuilder(Backup\MongoDB\MongoDBBackup::class)
                    ->onlyMethods($methods)
                    ->setConstructorArgs($constructorArguments)
                    ->getMock();
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
    
    public static function getMongoDBConnectionMock(array $methods = array()): MockObject
    {
        $instance = new self();

        return $instance
            ->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->onlyMethods($methods)
            ->getMock();
    }
}
