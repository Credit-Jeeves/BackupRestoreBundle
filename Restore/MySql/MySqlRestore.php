<?php

namespace ENC\Bundle\BackupRestoreBundle\Restore\MySql;

use ENC\Bundle\BackupRestoreBundle\Restore\AbstractRestore;
use ENC\Bundle\BackupRestoreBundle\Exception\RestoreException;

class MySqlRestore extends AbstractRestore
{
    const PARAM_KEY_HOST = 'host';
    const PARAM_KEY_PORT = 'port';
    const PARAM_KEY_USER = 'user';
    const PARAM_KEY_PASSWORD = 'password';
    const PARAM_KEY_DBNAME = 'dbname';

    /**
     * @throws RestoreException
     */
    public function restoreDatabase($file): void
    {
        if (!is_string($file)) {
            throw new \InvalidArgumentException('First argument must be a string with the full path to the SQL file.');
        }

        if (!is_file($file)) {
            throw new \InvalidArgumentException(sprintf('File "%s" does not exist.', $file));
        }

        $this->callVendorRestoreTool($file);
    }

    /**
     * @throws RestoreException
     */
    public function callVendorRestoreTool($file): void
    {
        if (!$this->doCallVendorRestoreTool($file)) {
            $exception = new RestoreException('An error occurred while working on the restore of your database. For more details, please look at the output of the command using the "getOutput" method of the exception.');
            $exception->setOutput($this->getLastCommandOutput());

            throw $exception;
        }
    }

    protected function doCallVendorRestoreTool($file): bool
    {
        $params = $this->getConnection()->getParams();
        $returnValue = '';
        $output = array();
        $commandToExecute = sprintf(
            'mysql --host="%s" --port="%s" --user="%s" --password="%s" %s < %s 2>&1;',
            $params[static::PARAM_KEY_HOST],
            $params[static::PARAM_KEY_PORT],
            $params[static::PARAM_KEY_USER],
            $params[static::PARAM_KEY_PASSWORD],
            $params[static::PARAM_KEY_DBNAME],
            $file
        );

        $returnLine = exec($commandToExecute, $output, $returnValue);

        $this->setLastCommandOutput($output);

        if ($returnValue !== 0) {
            return false;
        } else {
            return true;
        }
    }
}