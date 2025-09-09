import { PageProps as InertiaPageProps } from '@inertiajs/core';
import { usePage } from '@inertiajs/react';
import html2pdf from 'html2pdf.js';
import { useEffect, useRef } from 'react';

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
    const invoiceRef = useRef<HTMLDivElement>(null);
    const rentalLabel = ['Rental Name', 'Rental Unit Name', 'Type', 'Date From', 'Date To'];
    const secondRentalLabel = ['Time Start', 'Time End', 'Normal Price', 'Discount'];

    useEffect(() => {
        if (invoiceRef.current) {
            const opt = {
                margin: 0,
                filename: `Invoice-${booking.payment.order_id}.pdf`,
                image: { type: 'webp', quality: 1 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
            };
            html2pdf().from(invoiceRef.current).set(opt).save();
        }
    }, [booking]);
    return (
        <div ref={invoiceRef} className="relative mx-auto flex h-[295mm] w-[210mm] flex-col bg-white px-4 pt-0">
            <img src="/assets/invoice-assets/ty.png" className="absolute right-2 bottom-0 w-60" />
            <img src="/assets/invoice-assets/page-corner.png" className="absolute -top-5 -right-5 w-60" />
            <div className="flex flex-col gap-2 pt-20 pl-10">
                <p className="text-6xl font-bold">Brand</p>
                <p className="text-sm font-light">Segala Kebutuhan, Satu Tempat</p>
            </div>
            <div className="mt-24 flex justify-between px-10">
                <div className="flex flex-col">
                    <p className="text-sm font-extralight">Invoice To :</p>
                    <p className="text-xl font-bold">{booking.user.name}</p>
                    <p className="text-md font-light">{booking.user.email}</p>
                </div>
                <div className="flex flex-col">
                    <p className="text-2xl font-extrabold">INVOICE</p>
                    <div className="flex gap-2">
                        <div className="flex flex-col">
                            <p>Number</p>
                            <p>Date</p>
                        </div>
                        <div className="flex flex-col">
                            <p>:</p>
                            <p>:</p>
                        </div>
                        <div className="flex flex-col">
                            <p className="font-mono">{booking.payment.order_id}</p>
                            <p>
                                {new Date(booking.created_at).toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric',
                                })}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div className="mt-16 flex flex-col px-10">
                <p className="mb-3 text-2xl font-extrabold">Detail Rental :</p>
                <div className="flex gap-10">
                    <div className="flex gap-3">
                        <div className="flex flex-col">
                            {rentalLabel.map((x, i) => (
                                <p key={i}>{x}</p>
                            ))}
                        </div>
                        <div className="flex flex-col">
                            {rentalLabel.map((_, i) => (
                                <span key={i}>:</span>
                            ))}
                        </div>
                        <div className="flex flex-col">
                            <p className="font-bold">{booking.unit.rental.name}</p>
                            <p className="font-bold">{booking.unit.name}</p>
                            <p className="font-bold capitalize">{booking.unit.rental.type}</p>
                            <p className="font-bold">
                                {new Date(booking.start_time).toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: '2-digit',
                                    year: 'numeric',
                                })}
                            </p>
                            <p className="font-bold">
                                {new Date(booking.end_time).toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: '2-digit',
                                    year: 'numeric',
                                })}
                            </p>
                        </div>
                    </div>
                    <div className="flex gap-3">
                        <div className="flex flex-col">
                            {secondRentalLabel.map((x, i) => (
                                <p key={i}>{x}</p>
                            ))}
                        </div>
                        <div className="flex flex-col">
                            {secondRentalLabel.map((_, i) => (
                                <span key={i}>:</span>
                            ))}
                        </div>
                        <div className="flex flex-col">
                            <p className="font-bold">
                                {new Date(booking.start_time).toLocaleTimeString('id-ID', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                })}
                            </p>
                            <p className="font-bold">
                                {new Date(booking.end_time).toLocaleTimeString('id-ID', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                })}
                            </p>
                            <p className="font-bold">
                                {new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    minimumFractionDigits: 0,
                                }).format(booking.price)}
                            </p>
                            <p className="font-bold">{booking.discount}%</p>
                        </div>
                    </div>
                </div>
                <div className="my-5 flex bg-black text-white">
                    <p className="text-4xl font-black">
                        Total :{' '}
                        {new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0,
                        }).format(booking.final_price)}
                    </p>
                </div>
            </div>
        </div>
    );
}
