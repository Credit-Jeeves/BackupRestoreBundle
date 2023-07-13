<?php
namespace ENC\Bundle\BackupRestoreBundle\Tests\Restore\MySql;

use ENC\Bundle\BackupRestoreBundle\Exception\RestoreException;
use ENC\Bundle\BackupRestoreBundle\Tests\Restore\TestRestoreFactory;
use PHPUnit\Framework\TestCase;

class MySqlRestoreTest extends TestCase
{
    protected $tmpFile;

    protected function setUp(): void
    {
        $this->tmpFile = $this->createTmpFile();
    }

    protected function tearDown(): void
    {
        @unlink($this->tmpFile);
        $this->tmpFile = null;
    }
    
    public function test_restoreDatabase_passingNonStringArgument_throwsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $restoreInstance = TestRestoreFactory::getMock('mysql', array(
            'callRestoreVendorTool',
            'doCallRestoreVendorTool'
        ));
        
        $restoreInstance->restoreDatabase(123);
    }
    
    public function test_restoreDatabase_passingInvalidFile_throwsInvalidArgumentException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $restoreInstance = TestRestoreFactory::getMock('mysql', array(
            'callRestoreVendorTool',
            'doCallRestoreVendorTool'
        ));
        
        $restoreInstance->restoreDatabase('invalidFile');
    }
    
    public function test_restoreDatabase_callsCallVendorBackupToolInternallyWithCorrectArguments()
    {
        $this->expectNotToPerformAssertions();

        $restoreInstance = TestRestoreFactory::getMock('mysql', onlyMethods: array('callVendorRestoreTool'));
        $restoreInstance->expects($this->once())
            ->method('callVendorRestoreTool')
            ->with($this->tmpFile);
        
        $restoreInstance->restoreDatabase($this->tmpFile);
    }
    
    public function test_callVendorRestoreTool_throwsRestoreExceptionIfDoCallVendorRestoreToolProducedErrors()
    {
        $this->expectException(RestoreException::class);

        $restoreInstance = TestRestoreFactory::getMock('mysql', onlyMethods: ['getLastCommandOutput', 'doCallVendorRestoreTool']);
        $restoreInstance->expects($this->once())
            ->method('getLastCommandOutput')
            ->will($this->returnValue(array()));
        $restoreInstance->expects($this->once())
            ->method('doCallVendorRestoreTool')
            ->with($this->tmpFile)
            ->will($this->returnValue(false));
        
        $restoreInstance->restoreDatabase($this->tmpFile);
    }
    
    
    
    // Utility Methods
    public function createTmpFile()
    {
        $tmpDir = sys_get_temp_dir();
        $fileName = 'test_tmp_'.time().'_'.rand(1000, 9999).'.sql';
        $fullPath = $tmpDir.DIRECTORY_SEPARATOR.$fileName;
        
        if (!$handle = fopen($fullPath, 'w')) {
            throw new \RuntimeException('Tmp file for test could not be created.');
        }
        
        fclose($handle);
        
        return $fullPath;
    }
}