"use client";

import { useEffect, useState } from 'react';
import axios from 'axios';

const useSession = () => {
    const [user, setUser] = useState(null);

    useEffect(() => {
        const fetchSession = async () => {
            try {
                const response = await axios.get('https://carfix.ddev.site:448/api/session');
                setUser(response.data);
            } catch (error) {
                console.error('Error fetching session:', error);
            }
        };

        fetchSession();
    }, []);

    return user;
};

export default useSession;