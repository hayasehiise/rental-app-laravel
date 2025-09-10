// InvoiceDocument.tsx
import { Document, Image, Page, Text, View } from '@react-pdf/renderer';
import { createTw } from 'react-pdf-tailwind';

interface User {
    id: number;
    name: string;
    email: string;
}
interface Rental {
    id: number;
    name: string;
    type: string;
}
interface Unit {
    id: number;
    name: string;
    rental: Rental;
}
interface Payment {
    id: number;
    order_id: string;
}
interface Booking {
    id: number;
    start_time: string;
    end_time: string;
    price: number;
    discount: number;
    final_price: number;
    unit: Unit;
    user: User;
    payment: Payment;
    created_at: string;
}
interface InvoiceProps {
    booking: Booking; // sesuaikan dengan tipe booking kamu
}

const tw = createTw({});

export default function InvoiceTemplate({ booking }: InvoiceProps) {
    return (
        <Document>
            <Page size="LETTER" style={tw('p-10 bg-white flex flex-col')}>
                {/* Header */}
                <View style={tw('mb-10')}>
                    <Text style={tw('text-4xl font-bold leading-none')}>Brand</Text>
                    <Text style={tw('text-sm text-gray-600')}>Segala Kebutuhan, Satu Tempat</Text>
                </View>

                {/* Invoice Info */}
                <View style={tw('flex flex-row justify-between mb-10')}>
                    <View>
                        <Text style={tw('text-sm text-gray-500 mb-3')}>Invoice To:</Text>
                        <Text style={tw('text-lg font-bold leading-none')}>{booking.user.name}</Text>
                        <Text style={tw('text-sm px-3')}>{booking.user.email}</Text>
                    </View>
                    <View>
                        <Text style={tw('text-xl font-bold leading-none mb-3')}>INVOICE</Text>
                        <Text style={tw('text-sm')}>Number: {booking.payment.order_id}</Text>
                        <Text style={tw('text-sm')}>
                            Date:{' '}
                            {new Date(booking.created_at).toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric',
                            })}
                        </Text>
                    </View>
                </View>

                {/* Detail Rental */}
                <View style={tw('flex flex-col mt-32')}>
                    <Text style={tw('text-2xl font-bold mb-3 leading-none')}>Detail Rental :</Text>
                    <View style={tw('flex flex-row justify-between')}>
                        <View style={tw('text-sm gap-3')}>
                            <Text>Rental Name: {booking.unit.rental.name}</Text>
                            <Text>Unit: {booking.unit.name}</Text>
                            <Text style={tw('capitalize')}>Type: {booking.unit.rental.type}</Text>
                            <Text>Date From: {new Date(booking.start_time).toLocaleDateString('id-ID')}</Text>
                            <Text>Date To: {new Date(booking.end_time).toLocaleDateString('id-ID')}</Text>
                        </View>
                        <View style={tw('text-sm gap-3')}>
                            <Text>
                                Time Start:{' '}
                                {new Date(booking.start_time).toLocaleTimeString('id-ID', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                })}
                            </Text>
                            <Text>
                                Time End:{' '}
                                {new Date(booking.end_time).toLocaleTimeString('id-ID', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                })}
                            </Text>
                            <Text>
                                Normal Price:{' '}
                                {new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    minimumFractionDigits: 0,
                                }).format(booking.price)}
                            </Text>
                            <Text>Discount: {booking.discount}%</Text>
                        </View>
                    </View>
                    {/* Total */}
                    <View style={tw('mt-5 bg-black w-auto p-4')}>
                        <Text style={tw('text-white text-3xl font-bold leading-none')}>
                            Total:{' '}
                            {new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0,
                            }).format(booking.final_price)}
                        </Text>
                    </View>
                </View>
                {/* Background image (opsional) */}
                <Image
                    src="/assets/invoice-assets/ty.png"
                    style={{
                        position: 'absolute',
                        bottom: 20,
                        right: 20,
                        width: 120,
                    }}
                />
                <Image src={'/assets/invoice-assets/page-corner.png'} style={{ position: 'absolute', top: -15, right: -15, width: 150 }} />
            </Page>
        </Document>
    );
}
