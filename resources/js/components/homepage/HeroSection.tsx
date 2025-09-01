import { gsap } from 'gsap';
import { useEffect, useMemo, useRef, useState } from 'react';

export default function HeroSection() {
    const heroWords = ['Lapangan', 'Gedung', 'Kendaraan'];
    const bgImages = useMemo(() => ['/assets/hero-bg-1.webp', '/assets/hero-bg-2.webp', '/assets/hero-bg-3.webp'], []);
    const [wordIndex, setWordIndex] = useState(0);
    const [bgIndex, setBgIndex] = useState(0);
    const [showLayer1, setShowLayer1] = useState(true);
    const heroWordRef = useRef(null);
    const layer1Ref = useRef(null);
    const layer2Ref = useRef(null);

    // Animasi Text Slide
    useEffect(() => {
        const interval = setInterval(() => {
            gsap.to(heroWordRef.current, {
                x: -20,
                opacity: 0,
                duration: 0.5,
                onComplete: () => {
                    setWordIndex((prev) => (prev + 1) % heroWords.length);
                    gsap.fromTo(heroWordRef.current, { x: 20, opacity: 0 }, { x: 0, opacity: 1, duration: 0.5 });
                },
            });
        }, 2000);

        return () => clearInterval(interval);
    }, [heroWords.length]);

    // Animasi Untuk Hero Background
    useEffect(() => {
        const interval = setInterval(() => {
            const nextIndex = (bgIndex + 1) % bgImages.length;
            const topLayer = showLayer1 ? layer2Ref.current : layer1Ref.current;
            const bottomLayer = showLayer1 ? layer1Ref.current : layer2Ref.current;

            // Set gambar baru ke topLayer sebelum fade in
            topLayer.style.backgroundImage = `url(${bgImages[nextIndex]})`;
            gsap.to(topLayer, { opacity: 1, duration: 1 });
            gsap.to(bottomLayer, { opacity: 0, duration: 1 });

            setShowLayer1(!showLayer1);
            setBgIndex(nextIndex);
        }, 2000);
        return () => clearInterval(interval);
    }, [bgIndex, showLayer1, bgImages]);

    return (
        <div className="relative hero min-h-[100dvh] overflow-hidden">
            <div
                ref={layer1Ref}
                className="absolute inset-0 bg-cover bg-center"
                style={{
                    backgroundImage: `url(${bgImages[0]})`,
                    opacity: 1,
                }}
            ></div>
            <div
                ref={layer2Ref}
                className="absolute inset-0 bg-cover bg-center"
                style={{
                    backgroundImage: `url(${bgImages[1]})`,
                    opacity: 0,
                }}
            ></div>
            <div className="absolute hero-overlay"></div>
            <div className="hero-content text-center text-neutral-content">
                <div className="max-w-md">
                    <h1 className="mb-5 text-5xl font-bold">
                        Temukan Rental Yang Tepat:{' '}
                        <span ref={heroWordRef} className="inline-block">
                            {heroWords[wordIndex]}
                        </span>
                    </h1>
                    <p className="mb-5">Nikmati kemudahan reservasi dengan harga transparan dan layanan terpercaya</p>
                    <button className="btn btn-primary">Get Started</button>
                </div>
            </div>
        </div>
    );
}
