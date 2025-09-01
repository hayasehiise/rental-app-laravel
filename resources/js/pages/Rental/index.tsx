import Layout from '@/layouts/layout';
import { formatRupiah } from '@/utils/currency';
import { usePage } from '@inertiajs/react';
import { gsap } from 'gsap';
import { useEffect, useRef, useState } from 'react';
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

interface Rental {
    id: number;
    name: string;
    type: string;
    description: string;
    units: RentalUnit[];
    created_at: string;
}

interface RentalPageProps {
    rentals: Rental[];
    type: string | null;
}
function RentalUnitCard({ unit }: { unit: RentalUnit }) {
    const [currentIndex, setCurrentIndex] = useState(0);
    const imageRef = useRef<HTMLImageElement>(null);
    useEffect(() => {
        if (unit.image.length <= 1) return;

        const interval = setInterval(() => {
            const nextIndex = (currentIndex + 1) % unit.image.length;

            //fade out dulu
            gsap.to(imageRef.current, {
                opacity: 0,
                duration: 0.5,
                onComplete: () => {
                    setCurrentIndex(nextIndex);
                    // fade in setelah ganti index
                    gsap.fromTo(imageRef.current, { opacity: 0 }, { opacity: 1, duration: 0.5 });
                },
            });
        }, 2000);

        return () => clearInterval(interval);
    }, [currentIndex, unit.image.length]);

    return (
        <div className="card w-96 bg-base-100 shadow-xl">
            <figure className="relative h-48 w-full overflow-hidden">
                {unit.image.length > 0 ? (
                    <img
                        ref={imageRef}
                        key={unit.image[currentIndex].id}
                        src={`/storage/${unit.image[currentIndex].path}`}
                        alt={unit.name}
                        className="h-48 w-full object-cover"
                    />
                ) : (
                    <div className="flex h-48 w-full items-center justify-center bg-gray-200">No Image</div>
                )}
            </figure>
            <div className="card-body">
                <h2 className="card-title">{unit.name}</h2>
                <p className="text-lg font-semibold">{formatRupiah(unit.price)}</p>
                {/* Badge Status */}
                <div className="mt-2">
                    {unit.is_available ? (
                        <span className="badge badge-success">Tersedia</span>
                    ) : (
                        <span className="badge badge-error">Tidak Tersedia</span>
                    )}
                </div>
                <div className="card-action mt-4 justify-end">
                    <button className="btn btn-primary" disabled={!unit.is_available}>
                        {unit.is_available ? 'Checkout' : 'Rent Out'}
                    </button>
                </div>
            </div>
        </div>
    );
}
export default function RentalIndex() {
    const { rentals, type } = usePage<{ props: RentalPageProps }>().props;
    return (
        <Layout>
            <div className="mt-20 mb-5">
                <p className="text-4xl">Rental Page</p>
                <div className="mt-5 grid grid-cols-1 gap-10 px-10 md:grid-cols-2 lg:grid-cols-3">
                    {rentals.map((rental: Rental) => rental.units.map((unit) => <RentalUnitCard key={unit.id} unit={unit} />))}
                </div>
            </div>
        </Layout>
    );
}
