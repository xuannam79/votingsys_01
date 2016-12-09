<?php

namespace App\Console\Commands;

use Mail;
use File;
use Artisan;
use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email backup database file to admin';
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
        $emails = env('MAIL_USERNAME', 'fpoll.info@gmail.com');
        Artisan::call('db:backup');
        $files = File::allFiles(storage_path() . '/app/backups');

        foreach ($files as $file)
        {
            Mail::send('layouts.mail_backup_database_layouts', [], function ($message) use ($emails, $file) {
                $message->to($emails)->subject(trans('label.mail.backup_database.subject'));
                $message->attach((string)$file);
            });
        }

        //delete all file in storage
        File::deleteDirectory(storage_path() . '/app/backups');
    }
}
