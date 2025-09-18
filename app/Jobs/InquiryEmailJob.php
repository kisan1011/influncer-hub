<?php

namespace App\Jobs;

use App\Mail\InquiryMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class InquiryEmailJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  protected $details;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details)
    {
      $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $email = new InquiryMail($this->details);
        Mail::to($this->details['email'])->send($email);

      // $email = new InquiryMail();
      // // $email = new OtpMail($this->details);
      // // Mail::to($this->details['email'])->send($email)
      // Mail::to("Mrugesh@gmail.com")->send($email);
    }
}
