<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Appointment;
use SmsTo;
use Carbon\Carbon;
use App\Http\Controllers\AppointmentController as Appointments;

class AppointmentReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remind:case_managers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send alerts (Emails and SMS) to case managers, reminding them of due appointments';

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
      try {
        $apt = new Appointments;
        $appointments = $apt->getAppointments();
        $appts = $appointments->groupBy('email');
        foreach ($appts as $key => $value) {
            $appt_data = [
                            'email' => $key,
                            'appts' => $value
                        ];
            $this->sendMailReminder($appt_data);
            $this->sendSMSReminder($appt_data);
        }
        $this->info('Reminders sent!');
      } catch (Exception $e) {
          $this->info('Some went wrong: '.$e->getMessage());
      }
    }

     private function sendMailReminder($appt_data)
    {
        $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
        $beautymail->send('emails.appt_reminder', ['data'=>$appt_data], function($message) use ($appt_data)
        {
            $message
                ->from('info@cmams.com','FHI360 - Calabar Team')
                ->to($appt_data['email'])
                ->subject('Client appointment reminder');
        });
        return true;
    }

    private function sendSMSReminder($appt_data)
    {
        $apt = new Appointments;
        $appointments = $apt->getAppointments();
        $appts = $appointments->groupBy('phone');
        foreach ($appts as $key => $value) {
            $appt_data = [
                            'phone' => $key,
                            'appts' => $value
                        ];
        $this->send($appt_data);
        }
    }

    private function send($appt_data)
    {
        $msg = 'THIS WEEK\'S APPOINTMENTS REMINDER *-* ';
        foreach ($appt_data['appts'] as $apt) {
            $msg .= ' CLIENT NAME: '.$apt->client->name.', APPOINTMENT TYPE: '.ucfirst($apt->type).', DATE: '.Carbon::parse($apt->appt_date)->format('l jS \of F Y').' *-* ';
            $msg .= ' Please ensure to attend to all your appointments with the clients.';
            }
        $messages = [
            [
                'to' => $appt_data['phone'],
                'message' => $msg
            ],
        ];

        SmsTo::setMessages($messages)->sendSingle();
    }
}