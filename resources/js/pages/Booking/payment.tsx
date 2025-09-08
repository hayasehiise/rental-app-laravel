import Layout from '@/layouts/layout';
import { PageProps as InertiaPageProps } from '@inertiajs/core';
import { router, usePage } from '@inertiajs/react';
import { useEffect } from 'react';

declare global {
    interface Window {
        snap: any;
    }
}

interface Unit {
    id: number;
    name: string;
    price: number;
}
interface Payment {
    id: number;
    booking_id: number;
    order_id: string;
    transaction_status: string;
}
interface Booking {
    id: number;
    user_id: number;
    rental_unit_id: number;
    start_time: string;
    end_time: string;
    price: number;
    discount: number;
    final_price: number;
    unit: Unit;
    payment: Payment;
}
interface PageProps extends InertiaPageProps {
    snapToken: string;
    booking: Booking;
}
export default function PaymentPage() {
    const { snapToken, booking } = usePage<PageProps>().props;

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
                alert('Pembayaran Ditutup. Silahkan selesaikan pembayaran anda');
                window.location.href = route('rental.index');
            },
        });
    };
    return (
        <Layout>
            <div className="flex h-dvh w-full items-center justify-center">
                <div className="w-2xl p-8">
                    <h1 className="mb-2 text-xl font-bold">Pembayaran Booking</h1>
                    <div className="space-y-4">
                        <p>
                            <span className="font-semibold">Unit : </span>
                            {booking.unit.name}
                        </p>
                        <p>
                            <span className="font-semibold">Waktu Mulai : </span>
                            {new Date(booking.start_time).toLocaleString('id-ID')}
                        </p>
                        <p>
                            <span className="font-semibold">Waktu Mulai : </span>
                            {new Date(booking.end_time).toLocaleString('id-ID')}
                        </p>
                        <p>
                            <span className="font-semibold">Diskon : </span>
                            {Number(booking.discount)}%
                        </p>
                        <p>
                            <span className="font-semibold">Total Harga : </span>
                            Rp {Number(booking.final_price).toLocaleString()}
                        </p>
                    </div>
                    <div className="mt-4 flex gap-3">
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
