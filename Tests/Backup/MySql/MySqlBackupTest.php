<?php
namespace ENC\Bundle\BackupRestoreBundle\Tests\Backup\MySql;

use ENC\Bundle\BackupRestoreBundle\Exception\BackupException;
use ENC\Bundle\BackupRestoreBundle\Factory\BackupRestoreFactory;
use ENC\Bundle\BackupRestoreBundle\Tests\Backup\TestBackupFactory;
use ENC\Bundle\BackupRestoreBundle\Tests\Factory\TestBackupFactoryFactory;
use PHPUnit\Framework\TestCase;

class MySqlBackupTest extends TestCase
{
    public function test_backupDatabase_passingInvalidDirectory_throwsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $backupInstance = TestBackupFactory::getMock('mysql', array(
            'callVendorBackupTool'
        ));
        
        $backupInstance->backupDatabase('invalidDir');
    }
    
    public function test_backupDatabase_passingInvalidFilename_throwsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $backupInstance = TestBackupFactory::getMock('mysql', array(
            'callVendorBackupTool'
        ));
        
        $backupInstance->backupDatabase(sys_get_temp_dir(), new \DateTime());
    }
    
    public function test_backupDatabase_callsCallVendorBackupToolInternallyWithCorrectArguments()
    {
        $targetDir = sys_get_temp_dir();
        $fileName = 'backup.sql';
        $fullFilePath = $targetDir.DIRECTORY_SEPARATOR.$fileName;
        
        $connectionMock = TestBackupFactory::getDbalConnectionMock();
        
        $backupInstance = TestBackupFactory::getMock('mysql', array('callVendorBackupTool'), array($connectionMock));
        $backupInstance->expects($this->once())
            ->method('callVendorBackupTool')
            ->with($fullFilePath);
        
        $filePath = $backupInstance->backupDatabase($targetDir, $fileName);
        
        $this->assertEquals($filePath, $fullFilePath);
    }
    
    public function test_backupDatabase_worksWithFileNameAsNull()
    {
        $targetDir = sys_get_temp_dir();
        
        $connectionMock = TestBackupFactory::getDbalConnectionMock(['getDatabase']);
        $connectionMock
            ->expects($this->once())
            ->method('getDatabase')
            ->willReturn('test-db');
        
        $backupInstance = TestBackupFactory::getMock('mysql', array('callVendorBackupTool'), array($connectionMock));
        $backupInstance->expects($this->once())
            ->method('callVendorBackupTool');
        
        $filePath = $backupInstance->backupDatabase($targetDir);
        $result = strpos($filePath, $targetDir) !== false;
        
        $this->assertTrue($result);
    }
    
    public function test_callVendorBackupTool_callsDoCallVendorBackupToolInternallyWithCorrectArguments()
    {
        $targetDir = sys_get_temp_dir();
        $fileName = 'backup.sql';
        $fullFilePath = $targetDir.DIRECTORY_SEPARATOR.$fileName;
        
        $connectionMock = TestBackupFactory::getDbalConnectionMock();
        
        $backupInstance = TestBackupFactory::getMock('mysql', array('doCallVendorBackupTool'), array($connectionMock));
        $backupInstance->expects($this->once())
            ->method('doCallVendorBackupTool')
            ->with($fullFilePath)
            ->will($this->returnValue(true));
        
        $filePath = $backupInstance->backupDatabase($targetDir, $fileName);
        
        $this->assertEquals($filePath, $fullFilePath);
    }
    
    public function test_callVendorBackupTool_throwsBackupExceptionIfDoCallVendorBackupToolProducedErrors()
    {
        $this->expectException(BackupException::class);

        $targetDir = sys_get_temp_dir();
        $fileName = 'backup.sql';
        $fullFilePath = $targetDir.DIRECTORY_SEPARATOR.$fileName;
        
        $connectionMock = TestBackupFactory::getDbalConnectionMock();
        
        $backupInstance = TestBackupFactory::getMock('mysql', array('doCallVendorBackupTool', 'getLastCommandOutput'), array($connectionMock));
        $backupInstance->expects($this->once())
            ->method('doCallVendorBackupTool')
            ->with($fullFilePath)
            ->will($this->returnValue(false));
        $backupInstance->expects($this->once())
            ->method('getLastCommandOutput')
            ->will($this->returnValue(array()));
        
        $backupInstance->backupDatabase($targetDir, $fileName);
    }
}