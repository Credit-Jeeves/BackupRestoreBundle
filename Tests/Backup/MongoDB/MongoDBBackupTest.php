<?php
namespace ENC\Bundle\BackupRestoreBundle\Tests\Backup\MongoDB;

use ENC\Bundle\BackupRestoreBundle\Exception\BackupException;
use ENC\Bundle\BackupRestoreBundle\Factory\BackupRestoreFactory;
use ENC\Bundle\BackupRestoreBundle\Tests\Backup\TestBackupFactory;
use ENC\Bundle\BackupRestoreBundle\Tests\Factory\TestBackupFactoryFactory;
use PHPUnit\Framework\TestCase;

class MongoDBBackupTest extends TestCase
{
    public function test_backupDatabase_passingInvalidDirectory_throwsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $backupInstance = TestBackupFactory::getMock('mongodb', array(
            'callVendorBackupTool'
        ));
        
        $backupInstance->backupDatabase('invalidDir');
    }
    
    public function test_backupDatabase_passingValidDirectory_returnsPathToBackupIfNoExceptionWasThrown()
    {
        $tmpDir = sys_get_temp_dir();
        $pathToBackupMustBe = $tmpDir.'/dump';
        
        $backupInstance = TestBackupFactory::getMock('mongodb', array(
            'doCallVendorBackupTool'
        ));
        $backupInstance->expects($this->once())
            ->method('doCallVendorBackupTool')
            ->with($this->equalTo($pathToBackupMustBe))
            ->will($this->returnValue(true));
        
        $pathToBackup = $backupInstance->backupDatabase($tmpDir);
        
        $this->assertEquals($pathToBackup, $pathToBackupMustBe);
    }
    
    public function test_callVendorBackupTool_throwsBackupExceptionIfSomethingWentWrong()
    {
        $this->expectException(BackupException::class);

        $tmpDir = sys_get_temp_dir();
        $pathToBackupMustBe = $tmpDir.'/dump';
        
        $backupInstance = TestBackupFactory::getMock('mongodb', array(
            'doCallVendorBackupTool', 'getLastCommandOutput'
        ));
        $backupInstance->expects($this->once())
            ->method('getLastCommandOutput')
            ->will($this->returnValue(array()));
        $backupInstance->expects($this->once())
            ->method('doCallVendorBackupTool')
            ->with($this->equalTo($pathToBackupMustBe))
            ->will($this->returnValue(false));
        
        $backupInstance->backupDatabase($tmpDir);
    }
}