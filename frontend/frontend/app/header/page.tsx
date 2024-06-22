import Link from 'next/link';
import Image from 'next/image';
import "../globals.scss";

const Header = ({ user }) => {
    return (
        <header className="header">
            <div className="header__logo-container">
                <Link href="/"><Image src="/Dietify-logo.png" alt="Dietify logo" width={150} height={50} /></Link>
            </div>
            <div className="header__cases">
                <Link href="/howItWorks">HOW IT WORKS</Link>
                <Link href="/bmi">BMI</Link>
                <Link href="/mealplan">MEALPLAN</Link>
                <Link href="/profile">PROFILE</Link>
            </div>
            {user ? (
                <div className="header__login-container">
                    <Link href="/logout" className="btn-logout">LOG OUT</Link>
                </div>
            ) : (
                <div className="header__login-container">
                    <Link href="/login" className="login-button">LOGIN</Link>
                    <Link href="/register" className="signup-button">SIGN UP</Link>
                </div>
            )}
            <nav id="nav-menu" className="nav-menu">
                <div className="nav-menu__item"><Link href="/how-it-works">HOW IT WORKS</Link></div>
                <div className="nav-menu__item"><Link href="/bmi">BMI</Link></div>
                <div className="nav-menu__item"><Link href="/mealplan">MEALPLAN</Link></div>
                <div className="nav-menu__item"><Link href="/profile">PROFILE</Link></div>
                {user ? (
                    <div>
                        <Link href="/logout" className="btn-logout">LOG OUT</Link>
                    </div>
                ) : (
                    <div className="login__buttons-mobile">
                        <Link href="/login" className="login-button">LOGIN</Link>
                        <Link href="/register" className="signup-button">SIGN UP</Link>
                    </div>
                )}
            </nav>
            <div className="hamburger-menu">
                <button id="hamburger" aria-label="Open menu" aria-expanded="false">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </header>
    );
};

export default Header;
