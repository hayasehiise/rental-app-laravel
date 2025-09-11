import { Link, useForm, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { BsFloppy2 } from 'react-icons/bs';
import { IoEyeOff, IoEyeOutline, IoKey, IoKeyOutline, IoLogOut, IoMailOutline, IoPersonOutline } from 'react-icons/io5';
import { z } from 'zod';

interface RegisterError {
    name?: string;
    email?: string;
    password?: string;
    password_confirmation?: string;
}
export default function RegisterPage() {
    const { flash } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });
    const [showPassword, setShowPassword] = useState<boolean>(false);
    const [showConPassword, setShowConPassword] = useState<boolean>(false);
    const [clientError, setClientError] = useState<RegisterError>({});
    const registerSchema = z
        .object({
            name: z.string().min(1, 'Dimohon untuk memasukan nama anda'),
            email: z.email('Masukan alamat email yang benar'),
            password: z.string().min(6, 'Masukan minimal 6 karakter'),
            password_confirmation: z.string().min(6, 'Konfirmasi Password Harus Sama'),
        })
        .refine((data) => data.password === data.password_confirmation, {
            error: 'Password Tidak Sama',
            path: ['password', 'password_confirmation'],
        });

    const validateField = (field: keyof typeof data) => {
        const fieldSchema = registerSchema.pick({ [field]: true });
        const result = fieldSchema.safeParse({ [field]: data[field] });
        if (!result.success) {
            setClientError((prev) => ({ ...prev, [field]: result.error.flatten().fieldErrors[field]?.[0] || '' }));
        } else {
            setClientError((prev) => {
                const newError = { ...prev };
                delete newError[field];
                return newError;
            });
        }
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        const result = registerSchema.safeParse(data);

        if (!result.success) {
            const formatedError = result.error?.flatten().fieldErrors;
            const errorsObj: Record<string, string> = {};

            Object.entries(formatedError).forEach(([key, value]) => {
                if (value && value.length > 0) {
                    errorsObj[key] = value[0];
                }
            });

            setClientError(errorsObj);
            return;
        }
        setClientError({});
        post(route('register.store'));
    };

    return (
        <div className="mt-20 flex max-w-full justify-center p-6">
            <form onSubmit={submit} className="space-y-4">
                <fieldset className="fieldset w-sm rounded-box border border-base-300 bg-base-200 p-4">
                    <legend className="fieldset-legend text-2xl">Register Account</legend>

                    {flash?.success && <div className="mb-4 rounded bg-green-100 p-3 text-green-800">{flash.success}</div>}
                    {/* Client-side errors */}
                    {/* {Object.keys(clientError).length > 0 && (
                        <div className="mb-4 text-red-600">
                            {Object.values(clientError).map((err, i) => (
                                <p key={i}>{err}</p>
                            ))}
                        </div>
                    )} */}

                    {/* Server-side errors */}
                    {/* {errors && Object.keys(errors).length > 0 && (
                        <div className="mb-4 text-red-600">
                            {Object.values(errors).map((err, i) => (
                                <p key={i}>{err}</p>
                            ))}
                        </div>
                    )} */}

                    <label className="fieldset-label">Nama Lengkap</label>
                    <label className={`input w-full ${clientError.name && 'input-error'}`}>
                        <IoPersonOutline className="text-lg opacity-50" />
                        <input
                            type="text"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            onBlur={() => validateField('name')}
                            className=""
                        />
                    </label>
                    {clientError.name && <div className="px-3 text-red-400">{clientError.name}</div>}

                    <label className="fieldset-label">Email</label>
                    <label className={`input w-full ${clientError.email && 'input-error'}`}>
                        <IoMailOutline className="text-lg opacity-50" />
                        <input
                            type="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            onBlur={() => validateField('email')}
                            className=""
                        />
                    </label>
                    {clientError.email && <div className="px-3 text-red-400">{clientError.email}</div>}

                    <label className="fieldset-label">Password</label>
                    <label className={`input w-full ${clientError.password && 'input-error'}`}>
                        <IoKeyOutline className="text-lg opacity-50" />
                        <input
                            type={showPassword ? 'text' : 'password'}
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            onBlur={() => validateField('password')}
                            className=""
                        />
                        {!showPassword ? (
                            <IoEyeOutline onClick={() => setShowPassword(true)} className="cursor-pointer text-2xl" />
                        ) : (
                            <IoEyeOff onClick={() => setShowPassword(false)} className="cursor-pointer text-2xl" />
                        )}
                    </label>
                    {clientError.password && <div className="px-3 text-red-400">{clientError.password}</div>}

                    <label className="fieldset-label">Confirm Password</label>
                    <label className={`input w-full ${clientError.password_confirmation && 'input-error'}`}>
                        <IoKey className="text-lg opacity-50" />
                        <input
                            type={showConPassword ? 'text' : 'password'}
                            value={data.password_confirmation}
                            onChange={(e) => setData('password_confirmation', e.target.value)}
                            onBlur={() => validateField('password_confirmation')}
                            className=""
                        />
                        {!showConPassword ? (
                            <IoEyeOutline onClick={() => setShowConPassword(true)} className="cursor-pointer text-2xl" />
                        ) : (
                            <IoEyeOff onClick={() => setShowConPassword(false)} className="cursor-pointer text-2xl" />
                        )}
                    </label>
                    {clientError.password_confirmation && <div className="px-3 text-red-400">{clientError.password_confirmation}</div>}

                    <div className="mt-4 flex justify-center gap-5">
                        <button disabled={processing || Object.keys(clientError).length > 0} className="btn btn-primary">
                            <BsFloppy2 className="text-md" />
                            {processing ? 'Registering...' : 'Register'}
                        </button>
                        <Link className="btn btn-outline" href={route('login.user')}>
                            <IoLogOut className="text-xl" />
                            Cancel
                        </Link>
                    </div>
                </fieldset>
            </form>
        </div>
    );
}
