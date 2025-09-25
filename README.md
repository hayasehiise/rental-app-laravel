
# Rental Laravel Project

Project Aplikasi Rental Menggunakan Laravel + Inertia + Filament. berisi konten untuk Booking Penyewaan berupa Lapangan, Gedung, dan Kendaraan.




## Environment Variables

untuk Environment ada tambahan berupa:

// Midtrans Integration

MIDTRANS_SERVER_KEY=<Server Key dari Midtranss>
MIDTRANS_CLIENT_KEY=<Client Key dari Midtrans>
MIDTRANS_IS_PRODUCTION=false <- ubah true jika production

// Telegram Integration

TELEGRAM_BOT_TOKEN=<Bot Token>
TELEGRAM_CHAT_ID=<Chat Id Telegram>

// Frontend

VITE_MIDTRANS_CLIENT_KEY=<Client Key Midtrans>
VITE_VITE_MIDTRANS_IS_PRODUCTION=false
## Installation

Install project ini menggunakan composer dan npm/pnpm

```bash
    composer install
    npm install / pnpm install
    cd rental-app-laravel
```
## Run Locally

jika ingin menjalankan dalam development mode:

```bash
    composer run dev
```

jika ingin menjalankan tapi menggunakan midtrans nya. harus menggunakan ngrok atau sejenisnya:

```bash
    npm run build / pnpm build
    php artisan optimize:clear
    php artisan optimize
    php artisan serve
```
## Used By

This project is used by the following companies:

- BLU UPBU Bandara Internasional Mutiara SIS Al-Jufri
## License

[MIT](https://choosealicense.com/licenses/mit/)


## Authors

[@hayasehiise](https://github.com/hayasehiise)

