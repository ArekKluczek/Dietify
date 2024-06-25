import apiClient from './apiClient';

interface User {
    id: string;
    name: string;
    email: string;
    // Add other user properties as needed
}

export const fetchUserData = async (): Promise<User | null> => {
    const token = localStorage.getItem('token');
    if (!token) return null;

    try {
        const response = await apiClient.get<{ user: User }>('/user', {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });
        return response.data.user;
    } catch (error) {
        console.error('Error fetching user data:', error);
        return null;
    }
};
