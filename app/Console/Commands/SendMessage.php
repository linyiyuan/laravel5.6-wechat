<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ChatRoomController;

class SendMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:use {port}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '执行聊天端口监听';

    /**
     * 服务端Server监听端口
     * @var [integer]
     */
    protected static $port;
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
     * @return mixed
     */
    public function handle()
    {
       Self::$port =  $this->argument('port');

       ChatRoomController::server(Self::$port);
    }
}
