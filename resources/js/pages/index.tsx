import FeaturedRental from '@/components/homepage/FeaturedRental';
import HeroSection from '@/components/homepage/HeroSection';
import Layout from '@/layouts/layout';
import { Head } from '@inertiajs/react';

interface PropsType {
    totalUnits: number;
}
export default function Index(props: PropsType) {
    const { totalUnits } = props;
    return (
        <Layout>
            <Head title="Homepage" />
            <HeroSection />
            <FeaturedRental totalUnit={totalUnits} />
        </Layout>
    );
}
