<?php

class DbPatch_Task_Patch_SQL extends DbPatch_Task_Patch_Abstract
{
    protected $data = array(
        'filename' => null,
        'basename' => null,
        'patch_number' => null,
        'branch' => null,
        'description' => null,
    );

    public function apply()
    {

        //$connectionName = atkconfig('dbpatch_connection_name', 'default');
        //$config = atkconfig("db");


        $content = file_get_contents($this->filename);
        if ($content == '') {
            $this->error(
                sprintf('patch file %s is empty', $this->basename)
            );
            return false;
        }

        $database = $config[$connectionName]['db'];
        $user     = $config[$connectionName]['user'];
        $password = $config[$connectionName]['password'];
        $host     = $config[$connectionName]['host'];
        $port     = isset($config[$connectionName]['port']) ? $config[$connectionName]['port'] : '';


        if ($password != '') {
            $password = '-p'.$password;
        }

        if ($port != '') {
            $port = '-P'.$port;

        }

        $command = sprintf("mysql -h %s -u %s %s %s %s < '%s' 2>&1",
            $host,
            $user,
            $password,
            $port,
            $database,
            $patchFile['filename']
        );

        exec($command, $result, $return);

        if ($return <> 0) {

            $this->error(sprintf("invalid SQL in patch file %s:\n\n%s\n",
                $patchFile['basename'],
                implode("\n", $result)
            ));
            return false;
        }

        return true;

    }

    public function getType()
    {
        return 'SQL';
    }

    public function getDescription()
    {
        return $this->getComment(0);
    }

    public function create($description, $patchDirectory, $patchPrefix)
    {
        $filename = $this->getFilename($patchPrefix, strtolower($this->getType()));
        $content = '-- ' . $description . PHP_EOL;
        $this->writeFile($patchDirectory . $filename, $content);
    }

}
