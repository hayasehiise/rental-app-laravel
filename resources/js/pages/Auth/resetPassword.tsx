import { useForm } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { IoEyeOff, IoEyeOutline } from 'react-icons/io5';

export default function ResetPassword({ token, email }: { token: string; email: string }) {
    const { data, setData, post, processing, errors, setError, clearErrors } = useForm({
        email: email || '',
        password: '',
        password_confirmation: '',
        token: token || '',
    });
    const [confirmPass, setConfirmPass] = useState<boolean>(false);
    const [showPassword, setShowPassword] = useState<boolean>(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState<boolean>(false);

    useEffect(() => {
        if (data.password_confirmation && data.password_confirmation !== data.password) {
            setError('password_confirmation', 'Konfirmasi Password tidak sama dengan password baru');
        } else {
            clearErrors();
        }

        setConfirmPass(!!data.password);
    }, [clearErrors, setError, data.password, data.password_confirmation]);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('password.update'));
    };

    return (
        <div className="flex h-[100dvh] flex-col items-center justify-center">
            <form onSubmit={submit}>
                <fieldset className="fieldset w-xs rounded-box border border-base-300 bg-base-200 p-4">
                    <legend className="fieldset-legend text-lg">Change Password</legend>

                    <label className="label">New Password</label>
                    <label className="input w-full">
                        <input
                            type={showPassword ? 'text' : 'password'}
                            name="password"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                        />
                        {!showPassword ? (
                            <IoEyeOutline className="cursor-pointer text-xl" onClick={() => setShowPassword(true)} />
                        ) : (
                            <IoEyeOff className="cursor-pointer text-xl" onClick={() => setShowPassword(false)} />
                        )}
                    </label>

                    {confirmPass && (
                        <>
                            <label className="label">Confirm Password</label>
                            <label className="input w-full">
                                <input
                                    type={showConfirmPassword ? 'text' : 'password'}
                                    name="password_confirmation"
                                    value={data.password_confirmation}
                                    onChange={(e) => setData('password_confirmation', e.target.value)}
                                />
                                {!showConfirmPassword ? (
                                    <IoEyeOutline className="cursor-pointer text-xl" onClick={() => setShowConfirmPassword(true)} />
                                ) : (
                                    <IoEyeOff className="cursor-pointer text-xl" onClick={() => setShowConfirmPassword(false)} />
                                )}
                            </label>
                            {errors.password_confirmation && <p className="text-red-400">{errors.password_confirmation}</p>}
                        </>
                    )}

                    <button
                        className="btn mx-auto mt-3 w-[10rem] btn-neutral"
                        type="submit"
                        disabled={processing || Object.keys(errors).length > 0 || !data.password_confirmation}
                    >
                        Reset Password
                    </button>
                </fieldset>
            </form>
        </div>
    );
}
