import { useForm, usePage } from '@inertiajs/react';
import { FormEvent } from 'react';

interface FlashProps {
    status: string;
}
export default function ForgotPassword() {
    const { flash } = usePage<{ flash: FlashProps }>().props;
    const { data, setData, post, processing, errors } = useForm({ email: '' });

    const submit = (e: FormEvent) => {
        e.preventDefault();
        post('/forgot-password');
    };

    return (
        <div>
            <h1>Lupa Password</h1>
            {flash.status && <p style={{ color: 'green' }}>{flash.status}</p>}
            <form onSubmit={submit}>
                <input type="email" placeholder="Email" value={data.email} onChange={(e) => setData('email', e.target.value)} />
                {errors.email && <p>{errors.email}</p>}
                <button disabled={processing}>Kirim Link Reset</button>
            </form>
        </div>
    );
}
