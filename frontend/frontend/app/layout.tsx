'use client'
import { Inter } from 'next/font/google';
import Header from './components/Header';
import Footer from './components/Footer';
import './globals.scss';
import Chatbot from "@/app/components/Chatbot";
import {AuthProvider} from "@/app/services/authContext";

const inter = Inter({ subsets: ['latin'] });

export default function RootLayout({
                                       children,
                                   }: {
    children: React.ReactNode;
}) {
    return (
        <html lang="en">
        <body className={inter.className}>
        <AuthProvider>
            <Header />
            {children}
            <Chatbot />
            <Footer />
        </AuthProvider>
        </body>
        </html>
    );
}
