import Layout from '@/layouts/layout';
import { useForm, usePage } from '@inertiajs/react';
import { FormEvent, useEffect, useState } from 'react';

export default function BookingPage() {
    const { unit, bookings } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        booking_date: '',
        booking_time: '',
    });
    const [disabledTimes, setDisabledTimes] = useState<string[]>([]);

    useEffect(() => {
        if (!data.booking_date) {
            setDisabledTimes([]);
            return;
        }

        // bookings adalah array
        const booked = bookings.filter((b: any) => b.booking_date === data.booking_date).map((b: any) => b.booking_time);
        setDisabledTimes(booked);
    }, [data.booking_date, bookings]);

    function onSubmit(e: FormEvent) {
        e.preventDefault();
        post(route('booking.store', unit.id));
    }

    const availableTimes = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'];
    return (
        <Layout>
            <div className="mt-25 mb-5">
                <h1 className="mb-4 text-2xl font-bold">Booking {unit.name}</h1>
                <form onSubmit={onSubmit} className="space-y-4">
                    <fieldset className="fieldset w-sm rounded-box border border-base-300 bg-base-200 p-4">
                        <legend className="fieldset-legend text-xl font-semibold">Booking Form</legend>

                        <label className="label">Tanggal</label>
                        <input type="date" value={data.booking_date} onChange={(e) => setData('booking_date', e.target.value)} className="input" />
                        {errors.booking_date && <p className="text-red-400">{errors.booking_date}</p>}

                        <label className="label">Jam</label>
                        <select
                            className="select"
                            defaultValue={'Pilih Jam'}
                            value={data.booking_time}
                            onChange={(e) => setData('booking_time', e.target.value)}
                        >
                            <option disabled={true} value={''}>
                                Pilih Jam
                            </option>
                            {availableTimes.map((t) => (
                                <option value={t} key={t} disabled={disabledTimes.includes(t)}>
                                    {t} {disabledTimes.includes(t) ? '(Booked)' : ''}
                                </option>
                            ))}
                        </select>
                        {errors.booking_time && <p className="text-red-400">{errors.booking_time}</p>}

                        <div className="mt-4 flex">
                            <button type="submit" disabled={processing} className="btn btn-primary">
                                {processing ? '...Process' : 'Book & Pay'}
                            </button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </Layout>
    );
}
