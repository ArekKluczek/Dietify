'use client'
import React, { useEffect, useState, useCallback } from 'react';
import apiClient from "../services/apiClient";
import withAuth from '../services/auth';

interface ProfileData {
    id: string;
    height: string;
    weight: string;
    age: string;
    gender: string;
    activitylevel: string;
    dietpreferences: string;
    allergies: string;
}

const Profile: React.FC = () => {
    const [profile, setProfile] = useState<ProfileData | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchProfile = async () => {
            try {
                const response = await apiClient.get<ProfileData>('/profile');
                setProfile(response.data);
            } catch (error) {
                setError('Error fetching profile data.');
            } finally {
                setLoading(false);
            }
        };

        fetchProfile();
    }, []);

    const handleChange = useCallback((e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        if (profile) {
            setProfile({ ...profile, [e.target.name]: e.target.value });
        }
    }, [profile]);

    const handleSubmit = useCallback(async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        if (profile) {
            try {
                await apiClient.post('/profile', profile);
            } catch (error) {
                setError('Error updating profile.');
            }
        }
    }, [profile]);

    if (error) {
        return <div>{error}</div>;
    }

    return (
        <div>
            <div className="sidebar">
                <a href="/profile">Profile</a>
                <a href={`/user/${profile?.id}`}>Settings</a>
                <a href="/favourites">Favourites</a>
                <a href="/forum">Forum</a>
            </div>

            <div className="profile-form">
                <form onSubmit={handleSubmit} className="profile-form">
                    <div className="profile-form__items">
                        <div className="profile-form__item">
                            <label htmlFor="weight">Weight</label>
                            <input
                                type="number"
                                id="weight"
                                name="weight"
                                value={profile?.weight}
                                onChange={handleChange}
                                className="form-control"
                            />
                        </div>
                        <div className="profile-form__item">
                            <label htmlFor="height">Height</label>
                            <input
                                type="number"
                                id="height"
                                name="height"
                                value={profile?.height}
                                onChange={handleChange}
                                className="form-control"
                            />
                        </div>
                        <div className="profile-form__item">
                            <label htmlFor="age">Age</label>
                            <input
                                type="number"
                                id="age"
                                name="age"
                                value={profile?.age}
                                onChange={handleChange}
                                className="form-control"
                            />
                        </div>
                        <div className="profile-form__item">
                            <label htmlFor="gender">Gender</label>
                            <select
                                id="gender"
                                name="gender"
                                value={profile?.gender}
                                onChange={handleChange}
                                className="form-control"
                            >
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div className="profile-form__item">
                            <label htmlFor="activitylevel">Activity Level</label>
                            <input
                                type="text"
                                id="activitylevel"
                                name="activitylevel"
                                value={profile?.activitylevel}
                                onChange={handleChange}
                                className="form-control"
                            />
                        </div>
                        <div className="profile-form__item">
                            <label htmlFor="dietpreferences">Diet Preferences</label>
                            <input
                                type="text"
                                id="dietpreferences"
                                name="dietpreferences"
                                value={profile?.dietpreferences}
                                onChange={handleChange}
                                className="form-control"
                            />
                        </div>
                        <div className="profile-form__item">
                            <label htmlFor="allergies">Allergies</label>
                            <input
                                type="text"
                                id="allergies"
                                name="allergies"
                                value={profile?.allergies}
                                onChange={handleChange}
                                className="form-control"
                            />
                        </div>
                        <button type="submit" className="profile-form__button">SAVE</button>
                    </div>
                </form>
            </div>
            <div className="elipse-left"></div>
        </div>
    );
};

export default withAuth(Profile);
