<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use Illuminate\Support\Facades\Mail;
use App\Models\Message;



class ContactController extends Controller
{
    public $url = '/admin/contact';

    // Show the contact page
    public function show()
    {
        return view('contact_us');
    }
    public function send(ContactRequest $request)
    {
        $data = $request->validated();
        Message::storeFromRequest($data);


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
    }
    public function list(Request $request)
    {
        // Get the search parameter from the request
        $search = $request->input('search');

        // Define the mapping of headers to fields
        $headerMap = [
            'ID' => 'id',
            'Name' => 'name',
            'Email' => 'email',
            'Phone' => 'phone',
            'Message' => 'message',
            'Created At' => 'created_at',
        ];

        // Use the search scope defined in the Type model (assuming it's implemented)
        $data = Message::search($search, $headerMap)->paginate(10);

        // Define the headers for the table
        $headers = ['ID', 'Name', 'Email', 'Phone', 'Message', 'Created At'];

        // Prepare the rows by mapping through the types collection
        $rows = $data->map(function ($data) {
            return [
                'ID' => $data->id,
                'Name' => $data->name,
                'Email' => $data->email,
                'Phone' => $data->phone,
                'Message' => $data->message,
                'Created At' => $data->created_at->format('m/d/Y'),
            ];
        });

        $url = $this->url;

        // Return the view with headers and rows data
        return view('admin.contact.list', compact('headers', 'rows', 'data', 'search', 'url'));
    }
}
