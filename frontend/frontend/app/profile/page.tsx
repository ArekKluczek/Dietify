"use client";

import { useEffect, useState } from 'react';
import axios from 'axios';

const Profile = () => {
    const [profile, setProfile] = useState({
        height: '',
        weight: '',
        age: '',
        gender: '',
        activitylevel: '',
        dietpreferences: '',
        allergies: '',
    });

    useEffect(() => {
        axios.get('/api/profile')
            .then(response => {
                if (response.data && response.data.id) {
                    setProfile(response.data);
                } else {
                    console.error('Profile data does not include id:', response.data);
                }
            })
            .catch(error => console.error('Error fetching profile:', error));
    }, []);

    const handleChange = (e) => {
        setProfile({ ...profile, [e.target.name]: e.target.value });
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        axios.post('/api/profile', profile)
            .then(response => {
                console.log('Profile updated:', response.data);
            })
            .catch(error => {
                console.error('Error updating profile:', error);
            });
    };

    return (
        <div>
            <div className="sidebar">
                <a href="/profile">Profile</a>
                <a href={`/user/${profile.id}`}>Settings</a>
                <a href="/favourites">Favourites</a>
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
                                value={profile.weight}
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
                                value={profile.height}
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
                                value={profile.age}
                                onChange={handleChange}
                                className="form-control"
                            />
                        </div>
                        <div className="profile-form__item">
                            <label htmlFor="gender">Gender</label>
                            <select
                                id="gender"
                                name="gender"
                                value={profile.gender}
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
                                value={profile.activitylevel}
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
                                value={profile.dietpreferences}
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
                                value={profile.allergies}
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

export default Profile;
