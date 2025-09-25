import { Link, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { HiOutlineLogin } from 'react-icons/hi';
import { IoCaretBackOutline, IoClipboard, IoEyeOff, IoEyeOutline } from 'react-icons/io5';
import { z } from 'zod';

interface LoginError {
    email?: string;
    password?: string;
}
const loginSchema = z.object({
    email: z.email('Masukan Email Yang Benar'),
    password: z.string().min(6, 'Minimal Password 6 karakter'),
});
export default function LoginPage() {
    const { post, data, setData, processing, errors } = useForm({
        email: '',
        password: '',
    });
    const [showPassword, setShowPassword] = useState<boolean>(false);

    const [clientErrors, setClientErrors] = useState<LoginError>({});

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('login.user'));
    };

    // Validasi field tertentu saat blur
    const handleBlur = (field: 'email' | 'password') => {
        const result = loginSchema.pick({ [field]: true }).safeParse({
            [field]: field === 'email' ? data.email : data.password,
        });

        if (!result.success) {
            setClientErrors((prev) => ({
                ...prev,
                [field]: result.error.issues[0].message,
            }));
        } else {
            // Hapus error field tertentu kalau valid
            setClientErrors((prev) => {
                const copy = { ...prev };
                delete copy[field];
                return copy;
            });
        }
    };

    return (
        <div className="flex h-screen flex-col items-center justify-center">
            <form onSubmit={handleSubmit} className="space-y-4">
                <fieldset className="fieldset w-xs rounded-box border border-base-300 bg-base-200 p-4">
                    <legend className="fieldset-legend text-xl">Login</legend>

                    <label className="label">Email</label>
                    <input
                        type="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        onBlur={() => handleBlur('email')}
                        className="input"
                    />
                    {clientErrors.email && <p className="text-red-500">{clientErrors.email}</p>}
                    {errors.email && <p className="text-red-500">{errors.email}</p>}

                    <label className="label">Password</label>
                    <label className="input">
                        <input
                            type={showPassword ? 'text' : 'password'}
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            onBlur={() => handleBlur('password')}
                        />
                        {!showPassword ? (
                            <IoEyeOutline className="cursor-pointer text-xl" onClick={() => setShowPassword(true)} />
                        ) : (
                            <IoEyeOff className="cursor-pointer text-xl" onClick={() => setShowPassword(false)} />
                        )}
                    </label>
                    {clientErrors.password && <p className="text-red-500">{clientErrors.password}</p>}
                    {errors.password && <p className="text-red-500">{errors.password}</p>}

                    <div className="mt-4 flex justify-center gap-5">
                        <button className="btn btn-neutral" disabled={processing || Object.keys(clientErrors).length > 0}>
                            <HiOutlineLogin className="text-xl" />
                            {processing ? 'Logging In' : 'Login'}
                        </button>
                        <Link href={route('register.user')} className="btn btn-outline">
                            <IoClipboard className="text-xl" />
                            Register
                        </Link>
                    </div>
                </fieldset>
            </form>
            <p className="mt-2">
                Forgot your password?{' '}
                <Link className="font-bold decoration-wavy hover:underline" href={route('password.request')}>
                    Click Here
                </Link>
            </p>
            <Link href={route('home')} className="btn mt-2 btn-ghost">
                <IoCaretBackOutline className="text-xl" />
                Go Back
            </Link>
        </div>
    );
}
