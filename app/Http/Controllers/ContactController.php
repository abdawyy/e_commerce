<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use Illuminate\Support\Facades\Mail;



class ContactController extends Controller
{
     // Show the contact page
    public function show()
    {
        return view('contact_us');
    }
        public function send(ContactRequest $request)
    {
        $data = $request->validated();

        $this->sendToAdmin($data);
        $this->sendAutoReply($data);

        return back()->with('success', __('web.message_sent_successfully'));
    }

    // Handle the contact form submission
    protected function sendToAdmin(array $data): void
    {
        Mail::send([], [], function ($message) use ($data) {
            $body = "Someone wants to contact you:\n\n" .
                    "Name: {$data['name']}\n" .
                    "Email: {$data['email']}\n" .
                    "Phone: {$data['phone']}\n\n" .
                    "Message:\n{$data['message']}";

            $message->to('hayahhfashion@gmail.com')
                ->subject('New Contact Message from ' . $data['name'])
                ->text($body)
                ->replyTo($data['email']);
        });
    }

    protected function sendAutoReply(array $data): void
    {
        Mail::send([], [], function ($message) use ($data) {
            $body = "Dear {$data['name']},\n\n" .
                    "Thank you for reaching out to us. We have received your message and will contact you shortly.\n\n" .
                    "— Hayah";

            $message->to($data['email'])
                ->subject('Thank you for contacting Hayah')
                ->text($body)
                ->from('hayah@hayahfashion.net', 'Hayah Fashion');
        });
    }}
