/* eslint-disable react-hooks/exhaustive-deps */
import Layout from '@/layouts/layout';
import { PageProps as InertiaPageProps } from '@inertiajs/core';
import { Link, router, usePage } from '@inertiajs/react';
import { useVirtualizer } from '@tanstack/react-virtual';
import { gsap } from 'gsap';
import { useEffect, useRef, useState } from 'react';
interface RentalUnitImage {
    id: number;
    path: string;
}
interface RentalUnit {
    id: number;
    image: RentalUnitImage[];
}

interface RentalCategory {
    id: number;
    name: string;
    slug: string;
    description: string;
}

interface Rental {
    id: number;
    name: string;
    type: string;
    description: string;
    units: RentalUnit[];
    created_at: string;
    category: RentalCategory;
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
    const { rentals, type: initialType } = usePage<PageProps>().props;

    //State lokal untuk meyimpan list yang sudah diload
    const [item, setItem] = useState<Rental[]>(rentals.data);
    const [page, setPage] = useState(rentals.current_page);
    const [lastPage, setLastPage] = useState(rentals.last_page);
    const [loading, setLoading] = useState(false);
    // simpan type (bisa berubah kalau kamu punya filter UI)
    const [queryType] = useState<string | null>(initialType ?? null);

    // untuk infinite scroll
    const parentRef = useRef<HTMLDivElement>(null);
    const [columns, setColumns] = useState<number>(3);

    // Hitung jumlah kolom berdasarkan lebar container
    useEffect(() => {
        const resizeObserver = new ResizeObserver((entries) => {
            const width = entries[0].contentRect.width;
            if (width < 640) setColumns(1);
            else if (width < 1024) setColumns(2);
            else setColumns(3);
        });
        if (parentRef.current) resizeObserver.observe(parentRef.current);
        return () => resizeObserver.disconnect();
    }, []);

    const rows = Math.ceil(item.length / columns);

    const rowVirtualizer = useVirtualizer({
        count: rows,
        getScrollElement: () => parentRef.current,
        estimateSize: () => 560,
        overscan: 12,
    });

    function loadMore() {
        if (loading || page >= lastPage) return;
        setLoading(true);
        const params: Record<string, any> = { page: page + 1 };
        if (queryType) params.type = queryType;
        router.get(
            route('rental.index'),
            params,
            {
                preserveScroll: true,
                preserveState: true,
                replace: true,
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

    // infinite scroll
    useEffect(() => {
        const parent = parentRef.current;
        if (!parent) return;
        const onScroll = () => {
            if (parent.scrollHeight - parent.scrollTop - parent.clientHeight < 200) {
                loadMore();
            }
        };
        parent.addEventListener('scroll', onScroll);
        return () => parent.removeEventListener('scroll', onScroll);
    }, [page, lastPage, loading]);

    return (
        <Layout>
            <div className="mt-20 mb-5">
                <p className="mb-5 px-10 text-4xl">Our Rental Services</p>
                <div ref={parentRef} className="relative mx-auto h-[80vh] overflow-auto">
                    <div style={{ height: rowVirtualizer.getTotalSize(), position: 'absolute', width: '100%' }}>
                        {rowVirtualizer.getVirtualItems().map((virtualRow) => {
                            const start = virtualRow.index * columns;
                            const end = Math.min(start + columns, item.length);
                            const rowItems = item.slice(start, end);
                            return (
                                <div
                                    key={virtualRow.index}
                                    style={{
                                        position: 'absolute',
                                        top: 0,
                                        left: 0,
                                        width: '100%',
                                        transform: `translateY(${virtualRow.start}px)`,
                                        display: 'flex',
                                    }}
                                >
                                    {rowItems.map((rental) => (
                                        <div key={rental.id} className="flex w-full justify-center">
                                            <RentalCard rental={rental} />
                                        </div>
                                    ))}
                                </div>
                            );
                        })}
                    </div>
                    {loading && <p className="py-2 text-center">...Loading</p>}
                    {page >= lastPage && <p className="py-2 text-center">No More Data</p>}
                </div>
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
                    <div className="badge badge-info">{rental.category.name}</div>
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
