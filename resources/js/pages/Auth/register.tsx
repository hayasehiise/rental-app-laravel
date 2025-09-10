import { useForm, usePage } from '@inertiajs/react';

export default function RegisterPage() {
    const { flash } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('register.store'));
    };

    return (
        <div className="mx-auto mt-20 max-w-md rounded bg-white p-6 shadow">
            <h1 className="mb-6 text-2xl font-bold">Register</h1>

            {flash?.success && <div className="mb-4 rounded bg-green-100 p-3 text-green-800">{flash.success}</div>}

            {errors && Object.keys(errors).length > 0 && (
                <div className="mb-4 text-red-600">
                    {Object.values(errors).map((err, i) => (
                        <p key={i}>{err}</p>
                    ))}
                </div>
            )}

            <form onSubmit={submit} className="space-y-4">
                <input
                    placeholder="Name"
                    type="text"
                    value={data.name}
                    onChange={(e) => setData('name', e.target.value)}
                    className="w-full rounded border px-3 py-2"
                />
                <input
                    placeholder="Email"
                    type="email"
                    value={data.email}
                    onChange={(e) => setData('email', e.target.value)}
                    className="w-full rounded border px-3 py-2"
                />
                <input
                    placeholder="Password"
                    type="password"
                    value={data.password}
                    onChange={(e) => setData('password', e.target.value)}
                    className="w-full rounded border px-3 py-2"
                />
                <input
                    placeholder="Confirm Password"
                    type="password"
                    value={data.password_confirmation}
                    onChange={(e) => setData('password_confirmation', e.target.value)}
                    className="w-full rounded border px-3 py-2"
                />
                <button type="submit" disabled={processing} className="w-full rounded bg-blue-500 py-2 text-white hover:bg-blue-600">
                    {processing ? 'Registering...' : 'Register'}
                </button>
            </form>
        </div>
    );
}
