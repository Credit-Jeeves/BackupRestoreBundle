<?php
namespace ENC\Bundle\BackupRestoreBundle\Tests\Factory;

use ENC\Bundle\BackupRestoreBundle\Exception\UnsupportedDbalPlatformException;
use ENC\Bundle\BackupRestoreBundle\Exception\UnsupportedPlatformException;
use ENC\Bundle\BackupRestoreBundle\Tests\Factory\TestBackupRestoreFactoryFactory;
use PHPUnit\Framework\TestCase;

class BackupRestoreFactoryTest extends TestCase
{
    public function test_getBackupInstance_passingNonStringConnectionServiceID_throwsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $factory = TestBackupRestoreFactoryFactory::create();
        
        $factory->getBackupInstance(123);
    }
    
    public function test_getBackupInstance_passingAStringWithInvalidConnectionServiceID_throwsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $factory = TestBackupRestoreFactoryFactory::create();
        
        $factory->getBackupInstance('invalid_service_id');
    }
    
    public function test_getRestoreInstance_passingNonStringConnectionServiceID_throwsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $factory = TestBackupRestoreFactoryFactory::create();
        
        $factory->getRestoreInstance(123);
    }
    
    public function test_getRestoreInstance_passingAStringWithInvalidConnectionServiceID_throwsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $factory = TestBackupRestoreFactoryFactory::create();
        
        $factory->getRestoreInstance('invalid_service_id');
    }
    
    public function test_getBackupInstance_passingANonSupportedConnectionService_throwsUnsupportedPlatformException()
    {
        $this->expectException(UnsupportedPlatformException::class);

        $unsupportedServiceId = 'unsupported_service';
        $factory = TestBackupRestoreFactoryFactory::create(array(
            $unsupportedServiceId => new \DateTime()
        ));
        
        $factory->getBackupInstance($unsupportedServiceId);
    }
    
    public function test_getBackupInstance_passingANonSupportedDbalConnectionService_throwsUnsupportedDbalPlatformException()
    {
        $this->expectException(UnsupportedDbalPlatformException::class);

        $dbalConnection = TestBackupRestoreFactoryFactory::getDbalConnectionMock(array(
            'getDatabasePlatform'
        ));
        $dbalConnection->expects($this->once())
            ->method('getDatabasePlatform')
            ->will($this->returnValue(new \DateTime()));
        $dbalConnectionServiceId = 'dbal_unsupported';
        
        $factory = TestBackupRestoreFactoryFactory::create(array(
            $dbalConnectionServiceId => $dbalConnection
        ));
        
        $factory->getBackupInstance($dbalConnectionServiceId);
    }
    
    public function test_getRestoreInstance_passingANonSupportedConnectionService_throwsUnsupportedPlatformException()
    {
        $this->expectException(UnsupportedPlatformException::class);

        $unsupportedServiceId = 'unsupported_service';
        $factory = TestBackupRestoreFactoryFactory::create(array(
            $unsupportedServiceId => new \DateTime()
        ));
        
        $factory->getRestoreInstance($unsupportedServiceId);
    }
    
    public function test_getRestoreInstance_passingANonSupportedDbalConnectionService_throwsUnsupportedDbalPlatformException()
    {
        $this->expectException(UnsupportedDbalPlatformException::class);

        $dbalConnection = TestBackupRestoreFactoryFactory::getDbalConnectionMock(array(
            'getDatabasePlatform'
        ));
        $dbalConnection->expects($this->once())
            ->method('getDatabasePlatform')
            ->will($this->returnValue(new \DateTime()));
        $dbalConnectionServiceId = 'dbal_unsupported';
        
        $factory = TestBackupRestoreFactoryFactory::create(array(
            $dbalConnectionServiceId => $dbalConnection
        ));
        
        $factory->getRestoreInstance($dbalConnectionServiceId);
    }
    
    public function test_getBackupInstance_passingASupportedConnectionService_returnsInstanceOfBackupInterface()
    {
        $dbalConnection = TestBackupRestoreFactoryFactory::getDbalConnectionMock(array(
            'getDatabasePlatform'
        ));
        $dbalConnection->expects($this->once())
            ->method('getDatabasePlatform')
            ->will($this->returnValue(new \Doctrine\DBAL\Platforms\MySqlPlatform()));
        $dbalConnectionServiceId = 'dbal_supported';
        
        $factory = TestBackupRestoreFactoryFactory::create(array(
            $dbalConnectionServiceId => $dbalConnection
        ));
        
        $backupInstance = $factory->getBackupInstance($dbalConnectionServiceId);
        
        $this->assertInstanceOf('ENC\Bundle\BackupRestoreBundle\Backup\BackupInterface', $backupInstance);
    }
    
    public function test_getRestoreInstance_passingASupportedConnectionService_returnsInstanceOfRestoreInterface()
    {
        $dbalConnection = TestBackupRestoreFactoryFactory::getDbalConnectionMock(array(
            'getDatabasePlatform'
        ));
        $dbalConnection->expects($this->once())
            ->method('getDatabasePlatform')
            ->will($this->returnValue(new \Doctrine\DBAL\Platforms\MySqlPlatform()));
        $dbalConnectionServiceId = 'dbal_supported';
        
        $factory = TestBackupRestoreFactoryFactory::create(array(
            $dbalConnectionServiceId => $dbalConnection
        ));
        
        $RestoreInstance = $factory->getRestoreInstance($dbalConnectionServiceId);
        
        $this->assertInstanceOf('ENC\Bundle\BackupRestoreBundle\Restore\RestoreInterface', $RestoreInstance);
    }
}