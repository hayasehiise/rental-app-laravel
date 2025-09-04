import Layout from '@/layouts/layout';
import { router, usePage } from '@inertiajs/react';
import { useEffect } from 'react';

declare global {
    interface Window {
        snap: any;
    }
}
export default function PaymentPage() {
    const { snapToken, booking, unit } = usePage().props;

    useEffect(() => {
        const script = document.createElement('script');
        script.src =
            import.meta.env.VITE_MIDTRANS_IS_PRODUCTION === 'true'
                ? 'https://app.midtrans.com/snap/snap.js'
                : 'https://app.sandbox.midtrans.com/snap/snap.js';
        script.setAttribute('data-client-key', import.meta.env.VITE_MIDTRANS_CLIENT_KEY);
        document.body.appendChild(script);

        return () => {
            script.remove();
        };
    }, [snapToken]);

    const handlePay = () => {
        if (!window.snap) return;
        window.snap.pay(snapToken, {
            onSuccess: (result: any) => {
                window.location.href = route('rental.index'); // nanti ganti jadi page transaksi
            },
            onPending: (result: any) => {
                window.location.href = route('rental.index');
            },
            onError: (result: any) => {
                alert('Pembayaran Gagal. Coba Lagi');
                window.location.href = route('rental.index');
            },
            onClose: (result: any) => {
                alert('Pembayaran DItutup. Silahkan selesaikan pembayaran anda');
                window.location.href = route('rental.index');
            },
        });
    };
    return (
        <Layout>
            <div className="flex h-dvh w-full items-center justify-center">
                <div className="w-2xl p-8">
                    <h1 className="mb-2 text-xl font-bold">Pembayaran Booking</h1>
                    <p>Unit: {unit.name}</p>
                    <p>Tanggal: {booking.booking_date}</p>
                    <p>Jam: {booking.booking_time}</p>
                    <p>Jumlah: Rp {Number(unit.price).toLocaleString('id-ID')}</p>
                    <div className="mt-4 flex">
                        <button onClick={handlePay} className="btn btn-primary">
                            Bayar Sekarang
                        </button>
                        <button onClick={() => router.post(route('booking.cancel', booking.id))} className="btn btn-outline">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </Layout>
    );
}
