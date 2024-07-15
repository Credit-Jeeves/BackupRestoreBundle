<?php
namespace ENC\Bundle\BackupRestoreBundle\Backup\MongoDB;

use ENC\Bundle\BackupRestoreBundle\Common\MongoDB\MongoDBUtility;
use ENC\Bundle\BackupRestoreBundle\Backup\AbstractBackup;
use ENC\Bundle\BackupRestoreBundle\Exception\BackupException;
use ENC\Bundle\BackupRestoreBundle\Exception\UnsupportedPlatformException;

class MongoDBBackup extends AbstractBackup
{
    protected $utility;
    
    public function __construct($connection)
    {
        parent::__construct($connection);
        
        $this->utility = new MongoDBUtility();
    }
    
    public function backupDatabase($targetDirectory, $fileName = null)
    {
        if (!is_dir($targetDirectory)) {
            throw new \InvalidArgumentException(sprintf('Directory "%s" is not valid or it doesn\'t exist.', $targetDirectory));
        }

        $targetDirectory .= '/dump';
        
        $this->callVendorBackupTool($targetDirectory);
        
        return $targetDirectory;
    }
    
    protected function callVendorBackupTool($targetDirectory)
    {
        if (!$this->doCallVendorBackupTool($targetDirectory)) {
            $exception = new BackupException('An error occurred while working on the backup. For more details, please look at the output of the command using the "getOutput" method of the exception.');
            $exception->setOutput($this->getLastCommandOutput());
            
            throw $exception;
        }
    }
    
    protected function doCallVendorBackupTool($targetDirectory)
    {
        throw new UnsupportedPlatformException('Disabled due to a security issue. To enable it please uncomment code.');
        $connection = $this->getConnection();
        $serverParameters = $this->utility->extractParametersFromServerString($connection->getServer());
        $returnValue = '';
        $output = array();
        
        $commandToExecute = sprintf('mongodump --host "%s" %s %s --out %s', 
            $serverParameters['hostname'], 
            ($serverParameters['username'] !== '' ? '--username '.$serverParameters['username'] : ''),
            ($serverParameters['password'] !== '' ? '--password '.$serverParameters['password'] : ''),
            $targetDirectory);

        /*
         * This command has been disabled because mongo is unused in the R+B stack and is causing a security issue
         * to re-enable it  $returnLine = exec($commandToExecute, $output, $returnValue);
         */

        $this->setLastCommandOutput($output);
        
        if ($returnValue !== 0) {
            return false;
        } else {
            return true;
        }
    }
}