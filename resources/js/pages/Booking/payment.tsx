import { usePage } from '@inertiajs/react';
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

        script.onload = () => {
            if (!window.snap) return;
            window.snap.pay(snapToken, {
                onSuccess: (result: any) => {
                    window.location.href = route('rental.index'); // nanti ganti jadi page transaksi
                },
                onPending: (result: any) => {
                    window.location.href = route('rental.index');
                },
            });
        };
    });
    return (
        <div>
            <p>payment page</p>
        </div>
    );
}
