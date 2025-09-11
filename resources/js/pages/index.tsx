import FeaturedRental from '@/components/homepage/FeaturedRental';
import HeroSection from '@/components/homepage/HeroSection';
import Layout from '@/layouts/layout';
import { Head } from '@inertiajs/react';
import { useRef } from 'react';

interface PropsType {
    totalUnits: number;
}
export default function Index(props: PropsType) {
    const { totalUnits } = props;
    const featuredRef = useRef<HTMLDivElement>(null);

    const handleScroll = () => {
        featuredRef.current?.scrollIntoView({ behavior: 'smooth' });
    };
    return (
        <Layout>
            <Head title="Homepage" />
            <HeroSection onGetStarted={handleScroll} />
            <FeaturedRental totalUnit={totalUnits} ref={featuredRef} />
        </Layout>
    );
}
