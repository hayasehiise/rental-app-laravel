import { Link } from '@inertiajs/react';
import { useEffect, useRef } from 'react';

import { FaCar, FaHome, FaPhone, FaRegBuilding } from 'react-icons/fa';
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
                <div className="navbar bg-base-100 shadow-sm" ref={navbarRef}>
                    <div className="flex-1">
                        <Link href={'#'} className="btn btn-ghost">
                            Rental App
                        </Link>
                    </div>
                    <div className="flex-none">
                        <ul className="menu menu-horizontal pr-10">
                            <li>
                                <Link href={'#'}>
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
                                    <ul className="rounded-t-none bg-base-100 p-2">
                                        <li>
                                            <Link href="#">
                                                <MdOutlineStadium />
                                                Lapangan
                                            </Link>
                                        </li>
                                        <li>
                                            <Link href="#">
                                                <FaRegBuilding />
                                                Gedung
                                            </Link>
                                        </li>
                                        <li>
                                            <Link href="#">
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
        </main>
    );
}
