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
        $this->writer->line('apply patch: '. $this->basename);
        $content = file_get_contents($this->data['filename']);
        if ($content == '') {
            $this->writer->error(
                sprintf('patch file %s is empty', $this->data['basename'])
            );
            return false;
        }

        $config = $this->config->db->params;
        $database = $config->dbname;
        $user     = $config->username;
        $password = $config->password;
        $host     = $config->host;
        $port     = isset($config->port) ? $config->port : '';

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
            $this->data['filename']
        );

        exec($command, $result, $return);

        if ($return <> 0) {

            $this->writer->error(sprintf("invalid SQL in patch file %s:\n\n%s\n",
                $this->data['basename'],
                implode(PHP_EOL, $result)
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
        $patchNumberSize = $this->getPatchNumberSize($patchDirectory);
        $filename = $this->getPatchFilename($patchPrefix, strtolower($this->getType()), $patchNumberSize);
        $content = '-- ' . $description . PHP_EOL;
        $this->writeFile($patchDirectory . $filename, $content);
    }

}
