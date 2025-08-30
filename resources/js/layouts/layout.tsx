import { Link } from '@inertiajs/react';

export default function Layout({ children }) {
    return (
        <main>
            <header>
                <Link href="#">Home</Link>
                <Link href="#">About</Link>
                <Link href="#">Contact</Link>
            </header>
            <article>{children}</article>
        </main>
    );
}
