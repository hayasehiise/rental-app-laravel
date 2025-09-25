import { Link, useForm, usePage } from '@inertiajs/react';
import { gsap } from 'gsap';
import { useEffect, useRef, useState } from 'react';
import { IoCaretBackOutline } from 'react-icons/io5';
import { z } from 'zod';

interface FlashProps {
    status: string;
}

interface ClientErrors {
    email?: string;
}
const forgotPassSchema = z.object({
    email: z.email('Masukan email yang benar'),
});
export default function ForgotPassword() {
    const { flash } = usePage<{ flash: FlashProps }>().props;
    const { data, setData, post, processing, errors } = useForm({ email: '' });
    const [clientErrors, setClientErrors] = useState<ClientErrors>({});
    const statusRef = useRef<HTMLDivElement | null>(null);

    const handleEmailChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { value } = e.target;
        setData('email', value);

        const result = forgotPassSchema.safeParse({ email: value });
        if (!result.success) {
            const tree = z.treeifyError(result.error);
            setClientErrors({
                email: tree.properties?.email?.errors[0],
            });
        } else {
            setClientErrors({});
        }
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        const result = forgotPassSchema.safeParse(data);

        if (!result.success) {
            const tree = z.treeifyError(result.error);
            setClientErrors({
                email: tree.properties?.email?.errors[0],
            });

            return;
        }

        post(route('password.email'));
    };

    useEffect(() => {
        if (flash.status && statusRef.current) {
            const el = statusRef.current;
            gsap.fromTo(el, { y: 100, opacity: 0 }, { y: 0, opacity: 1, duration: 0.6, ease: 'power3.out' });

            // auto hide seetelah 3 detik
            const timer = setTimeout(() => {
                gsap.to(el, { y: 100, opacity: 0, duration: 0.6, ease: 'power3.in' });
            }, 3000);

            return () => clearTimeout(timer);
        }
    }, [flash.status]);

    return (
        <div className="flex h-[100dvh] flex-col items-center justify-center">
            {flash.status && (
                <div ref={statusRef} className="toast-end toast">
                    <div className="alert alert-success">{flash.status}</div>
                </div>
            )}
            <form onSubmit={submit}>
                <fieldset className="fieldset w-xs rounded-box border border-base-300 bg-base-200 p-4">
                    <legend className="fieldset-legend text-lg">Forgot Password</legend>

                    <label className="label">Email</label>
                    <input className="input w-full" type="email" value={data.email} onChange={handleEmailChange} />
                    {clientErrors.email && <p className="text-red-400">{clientErrors.email}</p>}
                    {errors.email && <p className="text-red-400">Email Tidak Terdaftar</p>}

                    <button
                        type="submit"
                        disabled={processing || Object.keys(clientErrors).length > 0}
                        className="btn mx-auto mt-3 w-[10rem] btn-neutral"
                    >
                        Send Link Reset
                    </button>
                </fieldset>
            </form>
            <Link href={route('login.user')} className="btn mt-2 btn-ghost">
                <IoCaretBackOutline className="text-xl" />
                Go Back
            </Link>
        </div>
    );
}
