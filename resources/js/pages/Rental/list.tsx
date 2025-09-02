/* eslint-disable react-hooks/exhaustive-deps */
import Layout from '@/layouts/layout';
import { formatRupiah } from '@/utils/currency';
import { Link, router, usePage } from '@inertiajs/react';
import { gsap } from 'gsap';
import { useEffect, useState } from 'react';
import { FaBookmark, FaCheckCircle, FaTimesCircle } from 'react-icons/fa';

interface RentalUnitImage {
    id: number;
    path: string;
}
interface RentalUnit {
    id: number;
    name: string;
    price: number;
    is_available: boolean;
    image: RentalUnitImage[];
}
interface PaginatedProps {
    data: RentalUnit[];
    current_page: number;
    last_page: number;
}
interface RentalUnitList {
    rental: {
        id: number;
    };
    units: PaginatedProps;
}
function UnitCard({ unit }: { unit: RentalUnit }) {
    const [index, setIndex] = useState(0);

    useEffect(() => {
        if (unit.image.length > 1) {
            const interval = setInterval(() => {
                setIndex((prev) => (prev + 1) % unit.image.length);
            }, 2500);
            return () => clearInterval(interval);
        }
    }, [unit.image.length]);

    useEffect(() => {
        gsap.fromTo(`unit-image-${unit.id}`, { opacity: 0 }, { opacity: 1, duration: 1, ease: 'power2.inOut' });
    }, [index, unit.id]);

    return (
        <div className="card w-96 bg-white shadow-sm">
            <figure>
                {unit.image.length > 0 ? (
                    <img src={`/storage/${unit.image[index].path}`} alt={unit.name} className={`h-64 w-full object-cover unit-image-${unit.id}`} />
                ) : (
                    <div className="flex h-64 items-center">
                        <h2 className="text-3xl text-gray-400">No Image</h2>
                    </div>
                )}
            </figure>
            <div className="card-body">
                <h2 className="card-title">
                    {unit.name} {unit.is_available ? <FaCheckCircle className="fill-success" /> : <FaTimesCircle className="fill-error" />}
                </h2>
                <p>{formatRupiah(unit.price)}</p>
                <div className="card-actions justify-end">
                    <Link href={'#'} className="btn btn-outline">
                        <FaBookmark />
                        Booking Now
                    </Link>
                </div>
            </div>
        </div>
    );
}
export default function ListUnitRental() {
    const { rental, units } = usePage<{ props: RentalUnitList }>().props;

    const [item, setItem] = useState<RentalUnit[]>(units.data);
    const [page, setPage] = useState(units.current_page);
    const [lastPage, setLastPage] = useState(units.last_page);
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
            route('rental.list', { id: rental.id }),
            { page: page + 1 },
            {
                preserveScroll: true,
                preserveState: true,
                only: ['units'],
                onSuccess: (res) => {
                    const newUnits = (res.props as any).units as PaginatedProps;
                    setItem((prev) => [...prev, ...newUnits.data]);
                    setPage(newUnits.current_page);
                    setLastPage(newUnits.last_page);
                },
                onFinish: () => setLoading(false),
            },
        );
    }
    return (
        <Layout>
            <div className="mt-20 mb-5">
                <p className="px-24 text-3xl font-extrabold">Rental Unit List</p>
                <div className="mt-5 grid grid-cols-1 gap-10 px-24 md:grid-cols-2 lg:grid-cols-3">
                    {item.map((unit) => (
                        <UnitCard key={unit.id} unit={unit} />
                    ))}
                </div>
                {loading && <p className="py-4 text-center">...Loading</p>}
                {page >= lastPage && <p className="py-4 text-center">No More Units</p>}
            </div>
        </Layout>
    );
}
