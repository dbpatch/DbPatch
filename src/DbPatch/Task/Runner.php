<?php
/**
 * Handle all the available tasks
 */ 
class DbPatch_Task_Runner
{
    protected $writer = null;

    public function __construct($writer)
    {
        $this->writer = $writer;
    }
    
    public function getWriter()
    {
        return $this->writer;
    }

    public function getTask($task, $options)
    {
        if (empty($task)) {
            throw new Exception('Please provide an action');
        }

        $class = 'DbPatch_Task_' . ucfirst($task);

        try {
            $task = new $class;
            $task->setWriter($this->getWriter())
                ->setOptions($options);

        } catch (Exception $e) {
            throw new Exception('Unknown task: '.$task);
        }
        return $task;
    }
    
    public function showHelp()
    {
        $writer = $this->getWriter();
        $writer->line('usage: dbpatch [--version] [--help] [--config=<file>] <command> [<args>]')
            ->line()
            ->line('The commands are:')
            ->indent(2)->line('install    install the changelog table')
            ->indent(2)->line('update     execute the patches')
            ->indent(2)->line('remove     remove a patch file from the changelog')
            ->indent(2)->line('sync       sync the changelog with the current patch files')
            ->indent(2)->line('show       show the contents of a patch file')
            ->indent(2)->line('status     show latest applied patches')
            ->line()
            ->line('see \'dbpatch help <command>\' for more information on a specific command');
    }
    
    public function showVersion()
    {
        $this->getWriter()->line('dbpatch ' . DbPatch_Core_Version::VERSION);
    }
}