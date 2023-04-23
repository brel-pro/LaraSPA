<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EchoEnv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laraspa:echoEnv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $aenv = $_ENV;

        foreach ($aenv as $arrKey => $arrData) {
            echo $arrKey.'   =   '.$arrData."\n";

        }

        return 0;
    }
}
