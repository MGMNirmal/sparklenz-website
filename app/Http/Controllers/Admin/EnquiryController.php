<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EnquiryStatus;
use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class EnquiryController extends Controller
{
    public function index(Request $request): View
    {
        $status = EnquiryStatus::tryFrom((string) $request->query('status'));

        return view('admin.enquiries.index', [
            'status' => $status,
            'enquiries' => Enquiry::query()
                ->when($status, fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'counts' => Enquiry::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function show(Enquiry $enquiry): View
    {
        if ($enquiry->status === EnquiryStatus::New) {
            $enquiry->update(['status' => EnquiryStatus::Read, 'read_at' => now()]);
        }

        return view('admin.enquiries.show', ['enquiry' => $enquiry]);
    }

    public function update(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate(['status' => ['required', new Enum(EnquiryStatus::class)]]);
        $status = EnquiryStatus::from($validated['status']);

        $enquiry->update([
            'status' => $status,
            'read_at' => $status === EnquiryStatus::New ? null : ($enquiry->read_at ?? now()),
            'replied_at' => $status === EnquiryStatus::Replied ? ($enquiry->replied_at ?? now()) : null,
        ]);

        return back()->with('status', 'Enquiry marked as '.strtolower($status->label()).'.');
    }
}
