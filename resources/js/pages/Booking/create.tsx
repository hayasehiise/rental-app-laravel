import Layout from '@/layouts/layout';
import { PageProps as InertiaPageProps } from '@inertiajs/core';
import { useForm, usePage } from '@inertiajs/react';
import { FormEvent, useCallback, useEffect, useState } from 'react';

interface Booking {
    id: number;
    user_id: number;
    rental_unit_id: number;
    start_time: string;
    end_time: string;
    status: 'pending' | 'paid' | 'cancelled';
}

interface Unit {
    id: number;
    name: string;
    price: number;
}

interface PageProps extends InertiaPageProps {
    unit: Unit;
    bookings: Booking[];
    errors: Record<string, string>;
}

export default function BookingPage() {
    const { unit, bookings, errors: pageErrors } = usePage<PageProps>().props;
    const { data, setData, post, processing, errors } = useForm({
        start_time: '',
        end_time: '',
    });
    const [dataError, setDataError] = useState<string | null>(null);

    // check overlap
    const isOverlapping = useCallback(
        (start: Date, end: Date): boolean => {
            return bookings.some((b) => {
                const bookedStart = new Date(b.start_time).getTime();
                const bookedEnd = new Date(b.end_time).getTime();
                console.log('B start' + bookedStart);
                console.log('B end ' + bookedEnd);
                console.log('start' + start.getTime());
                console.log('end' + end.getTime());
                return start.getTime() < bookedEnd && end.getTime() > bookedStart && ['pending', 'paid'].includes(b.status);
            });
        },
        [bookings],
    );

    // date validation
    useEffect(() => {
        if (data.start_time && data.end_time) {
            const start = new Date(data.start_time);
            const end = new Date(data.end_time);

            if (end <= start) {
                setDataError('Waktu Selesai harus lebih besar dari Waktu Mulai');
            } else if (start.getDay() === 6 || end.getDay() === 6) {
                setDataError('Tidak tersedia untuk hari sabtu');
            } else if (isOverlapping(start, end)) {
                setDataError('Rentang Waktu sudah ada yang isi');
            } else {
                setDataError(null);
            }
        }
    }, [data.start_time, data.end_time, isOverlapping]);

    function onSubmit(e: FormEvent) {
        e.preventDefault();

        if (dataError) return;

        post(route('booking.store', unit.id));
    }

    return (
        <Layout>
            <div className="mt-25 mb-5">
                <h1 className="mb-4 text-2xl font-bold">Booking {unit.name}</h1>
                <form onSubmit={onSubmit} className="space-y-4">
                    <fieldset className="fieldset w-sm rounded-box border border-base-300 bg-base-200 p-4">
                        <legend className="fieldset-legend text-xl font-semibold">Booking Form</legend>

                        {/* Waktu Mulai */}
                        <label className="label">Waktu Mulai</label>
                        <input
                            type="datetime-local"
                            value={data.start_time}
                            onChange={(e) => setData('start_time', e.target.value)}
                            className="input"
                        />
                        {errors.start_time && <p className="text-red-500">{errors.start_time}</p>}

                        {/* Waktu Selesai */}
                        <label className="label">Waktu Selesai</label>
                        <input type="datetime-local" value={data.end_time} onChange={(e) => setData('end_time', e.target.value)} className="input" />
                        {errors.end_time && <p className="text-red-500">{errors.end_time}</p>}

                        {/* Errors */}
                        <div className="mt-4 text-center">
                            {pageErrors.booking && <p className="text-red-400">{pageErrors.booking}</p>}
                            {dataError && <p className="text-red-400">{dataError}</p>}
                        </div>

                        <div className="mt-4 flex">
                            <button type="submit" disabled={processing || !!dataError} className="btn btn-primary">
                                {processing ? '...Process' : 'Book & Pay'}
                            </button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </Layout>
    );
}
