<?php


class DbPatch_Command_Dump extends DbPatch_Command_Abstract
{
    public function execute()
    {
        $filename = null;
        $database = $this->config->db->params->dbname;
        if ($this->console->issetOption('file')) {
            $filename = $this->console->getOptionValue('file', null);
        }
        if(is_null($filename)) {
            $filename = $database . '_' . date('Ymd_Hi'). '.sql';
        }

        $this->writer->line('Dumping database '.$database. ' to file '.$filename);
        $this->dumpDatabase($filename);
        return;
    }


    protected function dumpDatabase($filename)
    {
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
        $command = sprintf("mysqldump -h %s -u %s %s %s %s > '%s' 2>&1",
            $host,
            $user,
            $password,
            $port,
            $database,
            $filename
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

    public function showHelp()
    {
        parent::showHelp('dump');

        $writer = $this->getWriter();
        $writer->indent(2)->line('--file=<string>    Filename')
            ->line();
    }
}
