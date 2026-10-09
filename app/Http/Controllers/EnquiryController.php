<?php

namespace App\Http\Controllers;

use App\Enums\EnquiryStatus;
use App\Http\Requests\StoreEnquiryRequest;
use App\Mail\EnquiryReceived;
use App\Models\Enquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class EnquiryController extends Controller
{
    /**
     * Saves the enquiry first; the optional email notification can fail
     * without losing the enquiry or changing what the visitor is told.
     */
    public function store(StoreEnquiryRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('website');

        $enquiry = Enquiry::create($data + [
            'status' => EnquiryStatus::New,
            'ip_hash' => hash_hmac('sha256', (string) $request->ip(), (string) config('app.key')),
            'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
        ]);

        $recipient = config('sparklenz.enquiry_notify_to');

        if ($recipient) {
            try {
                Mail::to($recipient)->send(new EnquiryReceived($enquiry));
                $enquiry->forceFill(['notified_at' => now()])->save();
            } catch (Throwable $exception) {
                Log::warning('Enquiry notification failed', ['enquiry' => $enquiry->id, 'error' => $exception->getMessage()]);
            }
        }

        return redirect()->to(route('contact').'#enquiry')->with('enquiry_sent', true);
    }
}
