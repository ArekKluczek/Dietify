import Link from 'next/link';
import Image from 'next/image';

const Footer = () => {
    const currentYear = new Date().getFullYear();
    return (
        <footer className="footer mt-5">
            <div className="footer__container">
                <span> Arkadiusz Kluczek {currentYear}</span>
                <div className="footer__media">
                    <span>
                        <Link href="https://github.com/ArekKluczek/Dietify">
                            <Image src="/Github.svg" alt="Github" width={24} height={24}/>
                        </Link>
                    </span>
                    <span>
                        <Link href="https://www.linkedin.com/in/arek-kluczek">
                            <Image src="/Linkedin.svg" alt="Linkedin" width={24} height={24}/>
                        </Link>
                    </span>
                </div>
            </div>
        </footer>
    );
};

export default Footer;
