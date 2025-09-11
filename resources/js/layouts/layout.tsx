import { Link, router, usePage } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';

import { CiMenuBurger } from 'react-icons/ci';
import { FaCar, FaFacebook, FaGlobe, FaHome, FaInstagramSquare, FaPhone, FaRegBuilding } from 'react-icons/fa';
import { FiLogIn } from 'react-icons/fi';
import { MdOutlineStadium } from 'react-icons/md';
import { SiHomeassistantcommunitystore } from 'react-icons/si';

function getInitial(name: string) {
    if (!name) return '';

    const parts = name.trim().split(' ');

    const initials = parts
        .slice(0, 2)
        .map((part) => part.charAt(0).toUpperCase())
        .join('');

    return initials;
}
export default function Layout({ children }) {
    const { auth } = usePage<{ auth: { user: any } }>().props;
    const [showDrawer, setShowDrawer] = useState<boolean>(false);
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
                <div className="fixed top-0 z-[49] navbar h-5 bg-base-100 shadow-sm" ref={navbarRef}>
                    <div className="flex-1">
                        <Link href={'/'} className="btn btn-ghost">
                            Rental App
                        </Link>
                    </div>
                    {/* Menu jika tablet dan desktop view */}
                    <div className="hidden md:block md:flex-none">
                        <ul className="menu menu-horizontal gap-5 pr-10">
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
                            {auth?.user ? (
                                <div className="dropdown dropdown-end">
                                    <div tabIndex={0} role="button" className="btn avatar avatar-placeholder btn-circle btn-ghost">
                                        <div className="w-10 rounded-full bg-neutral text-neutral-content">
                                            <span>{getInitial(auth.user.name)}</span>
                                        </div>
                                    </div>
                                    <ul tabIndex={0} className="dropdown-content menu z-1 mt-3 w-52 menu-sm rounded-box bg-base-100 p-2 shadow">
                                        <li>
                                            <Link href={route('transaction.index')}>Transaction</Link>
                                        </li>
                                        <li>
                                            <a onClick={() => router.post(route('logout.user'))} className="hover:bg-red-500">
                                                Logout
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            ) : (
                                <button className="btn btn-outline" onClick={() => router.get(route('login.user'))}>
                                    <FiLogIn />
                                    Login
                                </button>
                            )}
                        </ul>
                    </div>
                    {/* Untuk menu mobile */}
                    <div className="flex-none md:hidden">
                        {/* <button className="btn btn-ghost">
                            <CiMenuBurger className="text-2xl" />
                        </button> */}
                        {/* Drawer */}
                        <div className="drawer">
                            <input id="nav-drawer" type="checkbox" className="drawer-toggle" />
                            <div className="drawer-content">
                                {/* Button */}
                                <label htmlFor="nav-drawer" className="drawer-button btn btn-ghost">
                                    <CiMenuBurger className="text-2xl" />
                                </label>
                            </div>
                            <div className="drawer-side">
                                <label htmlFor="nav-drawer" aria-label="close sidebar" className="drawer-overlay"></label>
                                <ul className="menu min-h-full w-80 bg-base-200 p-4 text-base-content">
                                    {/* Sidebar content here */}
                                    <li>
                                        <a>Sidebar Item 1</a>
                                    </li>
                                    <li>
                                        <a>Sidebar Item 2</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        {/* End Drawer */}
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
