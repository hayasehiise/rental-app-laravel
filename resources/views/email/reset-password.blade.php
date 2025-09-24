<x-mail::message>
    # Reset Password

    Anda menerima email ini akan ada permintaan reset password untuk akun anda.

    <x-mail::button :url="$url">
        Reset Password
    </x-mail::button>

    Jika anda tidak meminta reset password, abaikan email ini.

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
