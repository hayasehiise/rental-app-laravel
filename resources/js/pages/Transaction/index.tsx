import Layout from '@/layouts/layout';
import { PageProps as InertiaPageProps } from '@inertiajs/core';
import { router, usePage } from '@inertiajs/react';
import { ColumnDef, flexRender, getCoreRowModel, useReactTable } from '@tanstack/react-table';
import { useVirtualizer } from '@tanstack/react-virtual';
import { useEffect, useRef, useState } from 'react';

// Interface
interface Payment {
    id: number;
    order_id: string;
    transaction_status: string;
}
interface Unit {
    id: number;
    name: string;
    price: number;
}
interface ParentBooking {
    id: number;
    payment: Payment; // bisa null jika parent booking belum bayar
}
interface Booking {
    id: number;
    start_time: string;
    end_time: string;
    status: 'pending' | 'paid' | 'cancelled';
    final_price: number;
    unit: Unit;
    payment: Payment;
    parent_booking: ParentBooking;
}

interface PageProps extends InertiaPageProps {
    bookings: {
        data: Booking[];
        current_page: number;
        last_page: number;
    };
}
export default function TransactionPage() {
    const { bookings: initialBookings } = usePage<PageProps>().props;
    const [bookings, setBookings] = useState<Booking[]>(initialBookings.data);
    const [currentPage, setCurrentPage] = useState<number>(initialBookings.current_page);
    const [lastPage, setLastPage] = useState<number>(initialBookings.last_page);
    const [loading, setLoading] = useState<boolean>(false);
    const [colsWidth, setColsWidth] = useState<number[]>([]);

    const parentRef = useRef<HTMLDivElement>(null);

    // kolom table
    const columns: ColumnDef<Booking>[] = [
        {
            header: 'Nama Unit',
            accessorFn: (row) => row.unit.name,
        },
        {
            header: 'Order ID',
            accessorFn: (row) => row.payment?.order_id ?? row.parent_booking?.payment?.order_id,
            // cell: ({ row }) => {
            //     const booking = row.original;
            //     let orderId = '-';

            //     // Debug: uncomment baris ini untuk melihat data
            //     console.log('Booking data:', booking);

            //     if (booking.payment?.order_id) {
            //         orderId = booking.payment.order_id;
            //     } else if (booking.parent_booking?.payment?.order_id) {
            //         orderId = booking.parent_booking.payment.order_id;
            //     }

            //     return <span>{orderId}</span>;
            // },
        },
        {
            header: 'Jadwal Mulai',
            accessorFn: (row) => row.start_time,
        },
        {
            header: 'Jadwal Selesai',
            accessorFn: (row) => row.end_time,
        },
        {
            header: 'Status Pembayaran',
            accessorFn: (row) => row.status,
        },
        {
            header: 'Total Pembayaran',
            cell: ({ row }) => {
                return <p>Rp {row.original.final_price.toLocaleString('id-ID')}</p>;
            },
        },
        {
            header: 'Aksi',
            cell: ({ row }) => {
                const booking = row.original;
                return (
                    <div className="flex gap-2">
                        {booking.status === 'pending' && (
                            <button className="btn btn-sm btn-primary" onClick={() => router.visit(route('booking.payment', booking.id))}>
                                Bayar
                            </button>
                        )}
                        {booking.status === 'paid' && (
                            <button className="btn btn-outline btn-sm" onClick={() => window.open(route('invoice.download', booking.id))}>
                                Cetak
                            </button>
                        )}
                    </div>
                );
            },
        },
    ];

    // table nya
    const table = useReactTable({
        data: bookings,
        columns,
        getCoreRowModel: getCoreRowModel(),
    });

    // row Virtualizer untuk infinite scroll
    const rowVirtualizer = useVirtualizer({
        count: table.getRowModel().rows.length,
        getScrollElement: () => parentRef.current,
        estimateSize: () => 65,
        measureElement: (el) => el.getBoundingClientRect().height,
        overscan: 5,
    });

    // infinite scroll
    useEffect(() => {
        const handleScroll = async () => {
            if (!parentRef.current || loading || currentPage >= lastPage) return;

            const scrollOffset = parentRef.current.scrollTop + parentRef.current.clientHeight;
            if (scrollOffset + 100 >= parentRef.current.scrollHeight) {
                setLoading(true);
                const nextPage = currentPage + 1;
                try {
                    const res = await fetch(`/transaction?page=${nextPage}`);
                    const data: PageProps = await res.json();
                    setBookings((prev) => [...prev, ...data.bookings.data]);
                    setCurrentPage(data.bookings.current_page);
                    setLastPage(data.bookings.last_page);
                } finally {
                    setLoading(false);
                }
            }
        };

        const el = parentRef.current;
        el?.addEventListener('scroll', handleScroll);

        return () => el?.removeEventListener('scroll', handleScroll);
    }, [currentPage, lastPage, loading]);

    // hitung lebar kolom pada header
    const thRefs = useRef<(HTMLTableCellElement | null)[]>([]);
    useEffect(() => {
        if (thRefs.current.length > 0) {
            const widths = thRefs.current.map((th) => th?.offsetWidth ?? 0);
            setColsWidth(widths);
        }
    }, []);

    return (
        <Layout>
            <div className="mt-20 mb-10 px-10">
                <h1 className="mb-5 text-3xl font-bold">Transaksi Saya</h1>
                <div ref={parentRef} className="h-[70dvh] overflow-auto rounded-lg border">
                    <table className="table w-full table-zebra">
                        <thead>
                            {table.getHeaderGroups().map((headerGroup) => (
                                <tr key={headerGroup.id}>
                                    {headerGroup.headers.map((header, i) => (
                                        <th
                                            key={header.id}
                                            ref={(el) => {
                                                thRefs.current[i] = el;
                                            }}
                                        >
                                            {flexRender(header.column.columnDef.header, header.getContext())}
                                        </th>
                                    ))}
                                </tr>
                            ))}
                        </thead>
                        <tbody className="relative w-full">
                            {rowVirtualizer.getVirtualItems().map((virtualRow) => {
                                const row = table.getRowModel().rows[virtualRow.index];
                                return (
                                    <tr
                                        key={row.id}
                                        style={{
                                            position: 'absolute',
                                            top: 0,
                                            left: 0,
                                            width: '100%',
                                            transform: `translateY(${virtualRow.start}px)`,
                                        }}
                                    >
                                        {row.getVisibleCells().map((cell, i) => (
                                            <td key={cell.id} style={{ width: colsWidth[i] }}>
                                                {flexRender(cell.column.columnDef.cell, cell.getContext())}
                                            </td>
                                        ))}
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>
                </div>
            </div>
        </Layout>
    );
}
