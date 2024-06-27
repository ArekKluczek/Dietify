'use client'

import React, { useEffect, useState } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import apiClient from "../services/apiClient";
import '../globals.scss';
import { useRouter } from 'next/navigation';

interface User {
    id: string;
    name: string;
    email: string;
}

const Header: React.FC = () => {
    const [user, setUser] = useState<User | null>(null);
    const [isOpen, setIsOpen] = useState(false);
    const router = useRouter();

    useEffect(() => {
        const fetchUser = async () => {
            const token = localStorage.getItem('token');
            if (token) {
                try {
                    const response = await apiClient.get<User>('/user', {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    });
                    setUser(response.data);
                } catch (error) {
                    console.error('Error fetching user data:', error);
                    localStorage.removeItem('token');
                }
            }
        };

        fetchUser();
    }, []);

    const handleLogout = () => {
        localStorage.removeItem('token');
        router.push('/');
        setUser(null);
        setIsOpen(false); // Close menu on logout
    };

    const toggleMenu = () => {
        setIsOpen(!isOpen);
    };

    const closeMenu = () => {
        setIsOpen(false);
    };

    return (
        <header className="header">
            <div className="header__logo-container">
                <Link href="/" onClick={closeMenu}>
                    <Image src="/Dietify-logo.png" alt="Dietify logo" width={150} height={50} />
                </Link>
            </div>
            <div className="header__cases">
                <Link href="/howItWorks" onClick={closeMenu}>HOW IT WORKS</Link>
                <Link href="/bmi" onClick={closeMenu}>BMI</Link>
                <Link href="/mealPlan" onClick={closeMenu}>MEALPLAN</Link>
                <Link href="/profile" onClick={closeMenu}>PROFILE</Link>
                <Link href="/forum" onClick={closeMenu}>FORUM</Link>
            </div>
            {user ? (
                <div className="header__login-container">
                    <span>Welcome, {user.name}!</span>
                    <button onClick={handleLogout} className="logout">LOG OUT</button>
                </div>
            ) : (
                <div className="header__login-container">
                    <Link href="/login" className="login-button" onClick={closeMenu}>LOGIN</Link>
                    <Link href="/register" className="signup-button" onClick={closeMenu}>SIGN UP</Link>
                </div>
            )}
            <nav id="nav-menu" className={`nav-menu ${isOpen ? 'active' : ''}`}>
                <div className="nav-menu__item"><Link href="/howItWorks" onClick={closeMenu}>HOW IT WORKS</Link></div>
                <div className="nav-menu__item"><Link href="/bmi" onClick={closeMenu}>BMI</Link></div>
                <div className="nav-menu__item"><Link href="/mealPlan" onClick={closeMenu}>MEALPLAN</Link></div>
                <div className="nav-menu__item"><Link href="/profile" onClick={closeMenu}>PROFILE</Link></div>
                <div className="nav-menu__item"><Link href="/forum" onClick={closeMenu}>FORUM</Link></div>
                {user ? (
                    <div>
                        <button onClick={handleLogout} className="logout">LOG OUT</button>
                    </div>
                ) : (
                    <div className="login__buttons-mobile">
                        <Link href="/login" className="login-button" onClick={closeMenu}>LOGIN</Link>
                        <Link href="/register" className="signup-button" onClick={closeMenu}>SIGN UP</Link>
                    </div>
                )}
            </nav>
            <div className="hamburger-menu">
                <button id="hamburger" aria-label="Open menu" aria-expanded={isOpen} onClick={toggleMenu}>
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </header>
    );
};

export default Header;
