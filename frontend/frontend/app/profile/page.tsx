"use client";

import React, { useEffect, useState } from 'react';
import apiClient from '../apiClient';

const Profile = () => {
    const [profile, setProfile] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        apiClient.get('/profile')
            .then(response => {
                console.log('Profile data:', response.data);
                setProfile(response.data);
                setLoading(false);``
            })
            .catch(error => {
                console.error('Error fetching profile:', error);
                setError(error);
                setLoading(false);
            });
    }, []);

    if (loading) {
        return <div>Loading...</div>;
    }

    if (error) {
        return <div>Error: {error.message}</div>;
    }

    return (
        <div>
            <h1>Profile</h1>
            <pre>{JSON.stringify(profile, null, 2)}</pre>
        </div>
    );
};

export default Profile;
