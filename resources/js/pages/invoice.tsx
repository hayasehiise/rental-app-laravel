import InvoiceTemplate from '@/components/InvoiceTemplate';
import { PageProps as InertiaPageProps } from '@inertiajs/core';
import { usePage } from '@inertiajs/react';
import { pdf } from '@react-pdf/renderer';
import { useEffect } from 'react';

interface User {
    id: number;
    name: string;
    email: string;
}
interface Rental {
    id: number;
    name: string;
    type: string;
}
interface Unit {
    id: number;
    name: string;
    rental: Rental;
}
interface Payment {
    id: number;
    order_id: string;
}
interface Booking {
    id: number;
    start_time: string;
    end_time: string;
    price: number;
    discount: number;
    final_price: number;
    unit: Unit;
    user: User;
    payment: Payment;
    created_at: string;
}
interface PageProps extends InertiaPageProps {
    booking: Booking;
}
export default function InvoicePage() {
    const { booking } = usePage<PageProps>().props;

    useEffect(() => {
        const generatePdf = async () => {
            const blob = await pdf(<InvoiceTemplate booking={booking} />).toBlob();
            const url = URL.createObjectURL(blob);

            const a = document.createElement('a');
            a.href = url;
            a.download = `Invoice-${booking.payment.order_id}.pdf`;
            a.click();

            URL.revokeObjectURL(url);
        };

        generatePdf();
    }, [booking]);

    return <p>Sedang Menyiapkan Dokumen...</p>;
}
