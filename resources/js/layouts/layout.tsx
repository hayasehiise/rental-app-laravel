import { Link } from '@inertiajs/react';
import { useEffect, useRef } from 'react';

import { FaCar, FaFacebook, FaGlobe, FaHome, FaInstagramSquare, FaPhone, FaRegBuilding } from 'react-icons/fa';
import { MdOutlineStadium } from 'react-icons/md';
import { SiHomeassistantcommunitystore } from 'react-icons/si';

export default function Layout({ children }) {
    // Handle Sub Menu Navbar
    const navbarRef = useRef<HTMLDivElement>(null);
    useEffect(() => {
        function handleClickOutside(event: MouseEvent) {
            if (navbarRef.current) {
                const detailsElements = navbarRef.current.querySelectorAll<HTMLDetailsElement>('details');
                detailsElements.forEach((detail) => {
                    if (!detail.contains(event.target as Node)) {
                        detail.open = false;
                    }
                });
            }
        }

        document.addEventListener('click', handleClickOutside);

        return () => document.removeEventListener('click', handleClickOutside);
    }, []);

    return (
        <main>
            <header>
                <div className="fixed top-0 z-[49] navbar bg-base-100 shadow-sm" ref={navbarRef}>
                    <div className="flex-1">
                        <Link href={'/'} className="btn btn-ghost">
                            Rental App
                        </Link>
                    </div>
                    <div className="flex-none">
                        <ul className="menu menu-horizontal pr-10">
                            <li>
                                <Link href={'/'}>
                                    <FaHome />
                                    Home
                                </Link>
                            </li>
                            <li>
                                <details>
                                    <summary>
                                        <SiHomeassistantcommunitystore />
                                        Rental
                                    </summary>
                                    <ul className="z-50 rounded-t-none bg-base-100 p-2">
                                        <li>
                                            <Link href={route('rental.index', { type: 'lapangan' })}>
                                                <MdOutlineStadium />
                                                Lapangan
                                            </Link>
                                        </li>
                                        <li>
                                            <Link href={route('rental.index', { type: 'gedung' })}>
                                                <FaRegBuilding />
                                                Gedung
                                            </Link>
                                        </li>
                                        <li>
                                            <Link href={route('rental.index', { type: 'kendaraan' })}>
                                                <FaCar />
                                                Kendaraan
                                            </Link>
                                        </li>
                                    </ul>
                                </details>
                            </li>
                            <li>
                                <Link href={'#'}>
                                    <FaPhone />
                                    Contact Us
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>
            <article>{children}</article>
            <footer className="sm: footer footer-horizontal bg-neutral p-10 text-neutral-content">
                <aside>
                    <p className="text-3xl font-bold">Rental App</p>
                </aside>
                <nav>
                    <h6 className="footer-title">Social</h6>
                    <div className="grid grid-flow-col gap-4">
                        <Link href={'#'} className="flex items-center gap-2">
                            <FaInstagramSquare className="h-10 w-10" />
                        </Link>
                        <Link href={'#'} className="flex items-center gap-2">
                            <FaFacebook className="h-10 w-10" />
                        </Link>
                        <Link href={'#'} className="flex items-center gap-2">
                            <FaGlobe className="h-10 w-10" />
                        </Link>
                    </div>
                </nav>
            </footer>
        </main>
    );
}
