// components/withAuth.tsx
import React, { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import apiClient from './apiClient';

const withAuth = (WrappedComponent: React.ComponentType) => {
    return (props: any) => {
        const [isAuthenticated, setIsAuthenticated] = useState(false);
        const [loading, setLoading] = useState(true);
        const router = useRouter();

        useEffect(() => {
            const checkAuth = async () => {
                const token = localStorage.getItem('token');
                if (!token) {
                    router.push('/login');
                    return;
                }

                try {
                    await apiClient.get('/user', {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    });
                    setIsAuthenticated(true);
                } catch (error) {
                    localStorage.removeItem('token');
                    router.push('/login');
                } finally {
                    setLoading(false);
                }
            };

            checkAuth();
        }, [router]);

        if (loading) {
            return <div>Loading...</div>;
        }

        if (!isAuthenticated) {
            return null;
        }

        return <WrappedComponent {...props} />;
    };
};

export default withAuth;
