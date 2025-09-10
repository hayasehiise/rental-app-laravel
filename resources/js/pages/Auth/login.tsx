import { Link, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { z } from 'zod';

const loginSchema = z.object({
    email: z.email('Masukan Email Yang Benar'),
    password: z.string().min(6, 'Minimal Password 6 karakter'),
});
export default function LoginPage() {
    const { post, data, setData, processing, errors } = useForm({
        email: '',
        password: '',
    });

    const [clientErrors, setClientErrors] = useState<{ email: string; password: string }>({});

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
                    <input
                        type="password"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        onBlur={() => handleBlur('password')}
                        className="input"
                    />
                    {clientErrors.password && <p className="text-red-500">{clientErrors.password}</p>}
                    {errors.password && <p className="text-red-500">{errors.password}</p>}

                    <div className="mt-4 flex justify-center gap-5">
                        <button className="btn btn-neutral">{processing ? 'Logging In' : 'Login'}</button>
                        <Link href={'#'} className="btn btn-outline">
                            Register
                        </Link>
                    </div>
                </fieldset>
            </form>
        </div>
    );
}
