<h1># Hallo {{ $booking->user->name }}</h1>
<p>Berikut tautan untuk anda mengunduh invoice pemesanan anda :</p>
<a style="padding:10px 20px; background: #23a0cf; color:#ffffff; text-decoration:none; cursor:pointer; margin:5px 5px;"
    href="{{ route('invoice.download', $booking) }}">
    Unduh Invoice
</a>
<p>Terima Kasih</p>
