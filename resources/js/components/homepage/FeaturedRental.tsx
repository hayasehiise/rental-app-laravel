import { Link } from '@inertiajs/react';
import { forwardRef } from 'react';

import { FaBuilding, FaCar } from 'react-icons/fa';
import { MdArrowOutward, MdStadium } from 'react-icons/md';

interface PropsType {
    totalUnit: number;
}
const FeaturedRental = forwardRef<HTMLDivElement, PropsType>(({ totalUnit }, ref) => {
    return (
        <div ref={ref} className="min-h-[100dvh] lg:min-h-[80dvh]">
            <div className="flex flex-col">
                <p className="my-5 px-12 text-4xl font-bold lg:px-36">Our Rental List</p>
                <div className="flex flex-col items-center justify-center gap-10 px-0 md:flex-row md:px-5 lg:flex-row lg:px-0">
                    {/* Card Lapangan */}
                    <div className="card w-64 bg-base-100 shadow-sm lg:w-96">
                        <figure className="relative">
                            <img src="/assets/field-card.webp" className="h-48 w-full overflow-hidden object-cover" alt="Card figure for card" />
                            <div className="absolute top-2 right-2 badge badge-secondary">
                                <MdStadium />
                                Lapangan
                            </div>
                        </figure>
                        <div className="card-body">
                            <div className="card-title">Lapangan Serbaguna</div>
                            <p>Sewa lapangan untuk olahraga, event, atau gathering dengan fasilitas lengkap dan harga bersaing.</p>
                            <div className="card-action">
                                <Link href={route('rental.index', { type: 'lapangan' })} className="btn flex btn-ghost">
                                    More Details <MdArrowOutward className="h-5 w-5" />
                                </Link>
                            </div>
                        </div>
                    </div>
                    {/* Card Gedung/Ruangan */}
                    <div className="card w-64 bg-base-100 shadow-sm lg:w-96">
                        <figure className="relative">
                            <img src="/assets/room-card.webp" className="h-52 w-full overflow-hidden object-cover" alt="Card figure for card" />
                            <div className="absolute top-2 right-2 badge badge-secondary">
                                <FaBuilding />
                                Gedung
                            </div>
                        </figure>
                        <div className="card-body">
                            <div className="card-title">Gedung & Ruangan Acara</div>
                            <p>Tempat nyaman untuk meeting, seminar, pesta, atau acara keluarga dengan kapasitas fleksibel.</p>
                            <div className="card-action">
                                <Link href={route('rental.index', { type: 'gedung' })} className="btn flex btn-ghost">
                                    More Details <MdArrowOutward className="h-5 w-5" />
                                </Link>
                            </div>
                        </div>
                    </div>
                    {/* Card Kendaraan */}
                    <div className="card w-64 bg-base-100 shadow-sm lg:w-96">
                        <figure className="relative">
                            <img src="/assets/car-card.webp" className="h-52 w-full overflow-hidden object-cover" alt="Card figure for card" />
                            <div className="absolute top-2 right-2 badge badge-secondary">
                                <FaCar />
                                Kendaraan
                            </div>
                        </figure>
                        <div className="card-body">
                            <div className="card-title">Kendaraan Rental</div>
                            <p>Sewa mobil, bus, atau kendaraan lain untuk perjalanan pribadi maupun grup, cepat dan mudah.</p>
                            <div className="card-action">
                                <Link href={route('rental.index', { type: 'kendaraan' })} className="btn flex btn-ghost">
                                    More Details <MdArrowOutward className="h-5 w-5" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="flex justify-center">
                    <Link href={route('rental.index')} className="mx-ato btn my-5 w-42 rounded-none p-6 btn-primary">
                        ... {totalUnit} Units More
                    </Link>
                </div>
            </div>
        </div>
    );
});
export default FeaturedRental;
