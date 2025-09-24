import { useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

export default function ResetPassword({ token, email }: { token: string; email: string }) {
    const { data, setData, post, processing, errors } = useForm({
        email: email || '',
        password: '',
        password_confirmation: '',
        token: token || '',
    });

    const submit = (e: FormEvent) => {
        e.preventDefault();
        post(route('password.update'));
    };

    return (
        <div>
            <h1>Reset Password</h1>
            <form onSubmit={submit}>
                <input type="hidden" name="email" value={data.email} />
                <input
                    type="password"
                    name="password"
                    placeholder="Password Baru"
                    value={data.password}
                    onChange={(e) => setData('password', e.target.value)}
                />
                {errors.password && <p>{errors.password}</p>}

                <input
                    type="password"
                    name="password_confirmation"
                    placeholder="Konfirmasi Password"
                    value={data.password_confirmation}
                    onChange={(e) => setData('password_confirmation', e.target.value)}
                />
                <button disabled={processing}>Reset Password</button>
            </form>
        </div>
    );
}
