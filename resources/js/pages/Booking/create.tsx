import Layout from '@/layouts/layout';
import { useForm, usePage } from '@inertiajs/react';
import { FormEvent, useEffect, useState } from 'react';

export default function BookingPage() {
    const { unit, bookings, errors: pageErrors } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        booking_date: '',
        booking_time: '',
    });
    const [disabledTimes, setDisabledTimes] = useState<string[]>([]);
    const [isSaturday, setIsSaturday] = useState<boolean>(false);

    useEffect(() => {
        if (!data.booking_date) {
            setDisabledTimes([]);
            setIsSaturday(false);
            return;
        }

        const selectedDate = new Date(data.booking_date);
        setIsSaturday(selectedDate.getDay() === 6);

        // bookings adalah array
        const booked = bookings
            .filter((b: any) => b.booking_date === data.booking_date && ['pending', 'paid'].includes(b.status))
            .map((b: any) => b.booking_time.slice(0, 5));
        console.log(booked);
        setDisabledTimes(booked);
    }, [data.booking_date, bookings]);

    function onSubmit(e: FormEvent) {
        e.preventDefault();
        if (isSaturday) return;
        post(route('booking.store', unit.id));
    }

    const availableTimes = ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'];
    return (
        <Layout>
            <div className="mt-25 mb-5">
                <h1 className="mb-4 text-2xl font-bold">Booking {unit.name}</h1>
                {pageErrors.booking && <p className="text-red-400">{pageErrors.booking}</p>}
                <form onSubmit={onSubmit} className="space-y-4">
                    <fieldset className="fieldset w-sm rounded-box border border-base-300 bg-base-200 p-4">
                        <legend className="fieldset-legend text-xl font-semibold">Booking Form</legend>

                        <label className="label">Tanggal</label>
                        <input type="date" value={data.booking_date} onChange={(e) => setData('booking_date', e.target.value)} className="input" />
                        {errors.booking_date && <p className="text-red-400">{errors.booking_date}</p>}
                        {isSaturday && <p className="mt-1 text-sm text-red-400">Tidak Tersedia pada hari sabtu</p>}

                        <label className="label">Jam</label>
                        <select
                            className="select select-sm select-primary"
                            disabled={isSaturday}
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
                        {(errors.booking_time || pageErrors.booking_time) && <p className="text-red-400">{errors.booking_time}</p>}

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
