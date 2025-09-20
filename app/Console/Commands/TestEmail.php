<?php

namespace App\Console\Commands;

use App\Mail\PasswordResetByAdmin;
use App\Mail\SendEmailVerification;
use App\Mail\SendPasswordReset;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email?} {--type=password-reset} {--admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email functionality for password reset, admin password reset, and email verification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? $this->ask('Enter test email address');
        $type = $this->option('type');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address provided.');
            return 1;
        }

        // Create or find a test user
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => $email,
                'password' => bcrypt('password123'),
                'user_type' => 'buyer',
                'verified' => false,
            ]);
            $this->info("Created test user: {$user->email}");
        }

        $this->info("Testing email service with: {$email}");
        $this->info("Mail driver: " . config('mail.default'));
        $this->info("Mail host: " . config('mail.mailers.smtp.host'));

        try {
            $isAdmin = $this->option('admin');

            if ($isAdmin) {
                $this->testAdminPasswordResetEmail($user);
            } elseif ($type === 'password-reset') {
                $this->testPasswordResetEmail($user);
            } elseif ($type === 'email-verification') {
                $this->testEmailVerification($user);
            } else {
                $this->testPasswordResetEmail($user);
                $this->testEmailVerification($user);
            }

            $this->info('✅ Email test completed successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Email test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }

    private function testPasswordResetEmail(User $user)
    {
        $this->info('📧 Testing Password Reset Email...');

        // Generate a test token
        $token = Str::random(64);

        // Store the token
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => \Hash::make($token),
                'created_at' => now(),
            ]
        );

        Mail::to($user)->send(new SendPasswordReset($user, $token));
        $this->info('✅ Password reset email sent successfully!');
    }

    private function testAdminPasswordResetEmail(User $user)
    {
        $this->info('📧 Testing Admin Password Reset Email...');

        // Generate a password reset token
        $token = Password::createToken($user);

        // Create reset link
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
        $resetLink = $frontendUrl . '/auth/reset-password?token=' . $token . '&email=' . urlencode($user->email);

        Mail::to($user)->send(new PasswordResetByAdmin($user, $resetLink));
        $this->info('✅ Admin password reset email sent successfully!');
        $this->info('Reset link: ' . $resetLink);
    }

    private function testEmailVerification(User $user)
    {
        $this->info('📧 Testing Email Verification...');

        Mail::to($user)->send(new SendEmailVerification($user));
        $this->info('✅ Email verification sent successfully!');
    }
}
