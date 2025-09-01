import FeaturedRental from '@/components/homepage/FeaturedRental';
import HeroSection from '@/components/homepage/HeroSection';
import Layout from '@/layouts/layout';
import { Head } from '@inertiajs/react';

export default function Index(props) {
    return (
        <Layout>
            <Head title="Homepage" />
            <HeroSection />
            <FeaturedRental totalUnit={props.totalUnits} />
        </Layout>
    );
}
