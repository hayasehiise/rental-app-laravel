/* eslint-disable react-hooks/exhaustive-deps */
import Layout from '@/layouts/layout';
import { PageProps as InertiaPageProps } from '@inertiajs/core';
import { Link, router, usePage } from '@inertiajs/react';
import { gsap } from 'gsap';
import { useEffect, useState } from 'react';
interface RentalUnitImage {
    id: number;
    path: string;
}
interface RentalUnit {
    id: number;
    image: RentalUnitImage[];
}

interface Rental {
    id: number;
    name: string;
    type: string;
    description: string;
    units: RentalUnit[];
    created_at: string;
}
interface PaginatedRentals {
    data: Rental[];
    current_page: number;
    last_page: number;
}
interface PageProps extends InertiaPageProps {
    rentals: PaginatedRentals;
    type: string | null;
}
export default function RentalIndex() {
    const { rentals } = usePage<PageProps>().props;

    //State lokal untuk meyimpan list yang sudah diload
    const [item, setItem] = useState<Rental[]>(rentals.data);
    const [page, setPage] = useState(rentals.current_page);
    const [lastPage, setLastPage] = useState(rentals.last_page);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        function handleScroll() {
            if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200 && !loading && page < lastPage) {
                loadMore();
            }
        }
        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, [page, lastPage, loading]);

    function loadMore() {
        setLoading(true);
        router.get(
            route('rental.index'),
            { page: page + 1 },
            {
                preserveScroll: true,
                preserveState: true,
                only: ['rentals'],
                onSuccess: (res) => {
                    const newRentals = (res.props as any).rentals as PaginatedRentals;
                    setItem((prev) => [...prev, ...newRentals.data]);
                    setPage(newRentals.current_page);
                    setLastPage(newRentals.last_page);
                },
                onFinish: () => setLoading(false),
            },
        );
    }
    return (
        <Layout>
            <div className="mt-20 mb-5">
                <p className="px-10 text-4xl">Our Rental Services</p>
                <div className="mt-5 grid grid-cols-1 gap-10 px-10 md:grid-cols-2 lg:grid-cols-3">
                    {item.map((rental) => (
                        <RentalCard key={rental.id} rental={rental} />
                    ))}
                </div>
                {loading && <p className="py-4 text-center">...Loading</p>}
                {page >= lastPage && <p className="py-4 text-center">No More Data</p>}
            </div>
        </Layout>
    );
}

function RentalCard({ rental }: { rental: Rental }) {
    const image = rental.units.flatMap((u) => u.image);

    const [index, setIndex] = useState(0);

    useEffect(() => {
        if (image.length > 1) {
            const interval = setInterval(() => {
                setIndex((prev) => (prev + 1) % image.length);
            }, 2000);
            return () => clearInterval(interval);
        }
    }, [image.length]);

    useEffect(() => {
        gsap.fromTo(`.rental-image-${rental.id}`, { opacity: 0.4 }, { opacity: 1, duration: 1, ease: 'power2.inOut' });
    }, [index, rental.id]);

    return (
        <div className="card w-96 bg-white shadow-sm">
            <figure>
                {image.length > 0 ? (
                    <img src={`/storage/${image[index].path}`} alt={rental.name} className={`h-64 w-full object-cover rental-image-${rental.id}`} />
                ) : (
                    <div className="flex h-64 items-center">
                        <h2 className="text-3xl text-gray-400">No Image</h2>
                    </div>
                )}
            </figure>
            <div className="card-body">
                <h2 className="card-title">
                    {rental.name}
                    <div className="badge badge-info">{rental.type}</div>
                </h2>
                <p>{rental.description ?? 'Tanpa Deskripsi'}</p>
                <div className="card-actions justify-end">
                    <Link href={route('rental.list', rental.id)} className="btn btn-ghost">
                        More Units
                    </Link>
                </div>
            </div>
        </div>
    );
}
