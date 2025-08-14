<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mail {email : The email address to send test mail to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify mail configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('Testing mail configuration...');
        $this->info('SMTP Host: ' . config('mail.mailers.smtp.host'));
        $this->info('SMTP Port: ' . config('mail.mailers.smtp.port'));
        $this->info('From Address: ' . config('mail.from.address'));
        $this->info('From Name: ' . config('mail.from.name'));
        $this->newLine();
        
        try {
            Mail::raw('This is a test email from SCM Medquest system. If you received this, your mail configuration is working correctly!', function ($message) use ($email) {
                $message->to($email)
                       ->subject('SCM Medquest - Mail Configuration Test')
                       ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            $this->info("✅ Test email sent successfully to: {$email}");
            $this->info('Please check your email inbox and spam folder.');
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email!');
            $this->error('Error: ' . $e->getMessage());
            
            // Additional debugging information
            $this->newLine();
            $this->warn('Debugging Information:');
            $this->line('Mail Driver: ' . config('mail.default'));
            $this->line('Mail Host: ' . config('mail.mailers.smtp.host'));
            $this->line('Mail Port: ' . config('mail.mailers.smtp.port'));
            $this->line('Mail Username: ' . config('mail.mailers.smtp.username'));
            $this->line('Mail Encryption: ' . config('mail.mailers.smtp.encryption'));
        }
    }
}
