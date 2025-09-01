import HeroSection from '@/components/homepage/HeroSection';
import Layout from '@/layouts/layout';
import { Head } from '@inertiajs/react';

export default function Index() {
    return (
        <Layout>
            <Head title="Homepage" />
            <HeroSection />
        </Layout>
    );
}
