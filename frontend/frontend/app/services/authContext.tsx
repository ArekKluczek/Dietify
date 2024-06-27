'use client';
import React, { createContext, useState, useContext, useEffect } from 'react';
import apiClient from './apiClient';

interface AuthContextType {
    user: { email: string } | null;
    setUser: React.Dispatch<React.SetStateAction<{ email: string } | null>>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
    const [user, setUser] = useState<{ email: string } | null>(null);

    useEffect(() => {
        const fetchUser = async () => {
            try {
                const response = await apiClient.get('/user');
                setUser(response.data);
            } catch (error) {
                console.error('Error fetching user:', error);
            }
        };

        fetchUser();
    }, []);

    return <AuthContext.Provider value={{ user, setUser }}>{children}</AuthContext.Provider>;
};

export const useAuth = () => {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
};
