New enquiry from the Sparklenz website

Name: {{ $enquiry->name }}
Email: {{ $enquiry->email }}
Phone: {{ $enquiry->phone ?? '—' }}
Interested in: {{ $enquiry->service_interest ?? '—' }}
Received: {{ $enquiry->created_at->timezone('Asia/Colombo')->format('j M Y, g:i a') }}

{{ $enquiry->message }}

Open the admin area to mark it as read or replied: {{ route('admin.enquiries.show', $enquiry) }}
