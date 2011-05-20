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

    public function getTask($action, $configFilename = null)
    {
        $class = null;
        
        switch($action) {
            case 'install' : // install
                $class = 'DbPatch_Task_Install';
                break;
            case 'reinstall' : // reinstall
                $class = 'DbPatch_Task_Reinstall';
                break;
            case 'remove' : // remove patch
                $class = 'DbPatch_Task_Remove';
                break;
            case 'status' : // Status
                $class = 'DbPatch_Task_Status';
                break;
            case 'sync' : // Status
                $class = 'DbPatch_Task_Sync';
                break;
            case 'upgrade' : // Upgrade'
                $class = 'DbPatch_Task_Upgrade';
                break;
            default:
                throw new Exception('unknown task');
        }

        $task = new $class;
        $task->setWriter($this->getWriter());
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
            ->line()
            ->line('see \'dbpatch help <command>\' for more information on a specific command');
    }
    
    public function showVersion()
    {
        $this->getWriter()->line('dbpatch ' . DbPatch_Core_Version::VERSION);
    }
}