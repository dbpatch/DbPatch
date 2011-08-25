<?php
/**
 * Handle all the available commands
 */ 
class DbPatch_Command_Runner
{
    protected $writer = null;

    static public function getValidCommands()
    {
        return array('help', 'create', 'remove', 'show', 'status', 'sync', 'update', 'dump');

    }

    public function __construct($writer)
    {
        $this->writer = $writer;
    }
    
    public function getWriter()
    {
        return $this->writer;
    }

    public function getCommand($command, $console)
    {
        if (empty($command) || !in_array($command, self::getValidCommands())) {
            throw new Exception('Please provide a valid command');
        }

        $class = 'DbPatch_Command_' . ucfirst(strtolower($command));

        try {
            $command = new $class;
            $command->setWriter($this->getWriter())
                ->setConsole($console);

        } catch (Exception $e) {
            throw new Exception('Unknown command: '.$command);
        }
        return $command;
    }
    
    public function showHelp()
    {
        $writer = $this->getWriter();
        $writer->line()->version();
        $writer->line('usage: dbpatch [--version] [--help] [--config=<file>] [--color] <command> [<args>]')
            ->line()
            ->line('The commands are:')
            ->indent(2)->line('update     execute the patches')
            ->indent(2)->line('create     create empty patch file')
            ->indent(2)->line('remove     remove a patch file from the changelog')
            ->indent(2)->line('sync       sync the changelog with the current patch files')
            ->indent(2)->line('show       show the contents of a patch file')
            ->indent(2)->line('status     show latest applied patches')
            ->indent(2)->line('dump       dump database')
            ->line()
            ->line('see \'dbpatch help <command>\' for more information on a specific command');
    }
}
