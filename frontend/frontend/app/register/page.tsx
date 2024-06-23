'use client'

import { useState } from 'react';
import apiClient from '../apiClient';
import {router} from "next/client";
import {useRouter} from "next/navigation";

const Register = () => {
    const [formData, setFormData] = useState({
        email: '',
        plainPassword: '',
        secondPassword: '',
    });

    const [error, setError] = useState(null);
    const [success, setSuccess] = useState(null);
    const router = useRouter();

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            console.log('Submitting form data:', formData);
            const response = await apiClient.post('/register', formData);
            console.log('Response:', response);
            setSuccess(response.data.message);
            setError(null);
            router.push('/login');
        } catch (error) {
            console.error('Error:', error.response || error.message);
            setError(error.response?.data.errors || 'An error occurred');
            setSuccess(null);
        }
    };

    return (
        <div className="form-container">
            <h1 className="h3 mb-3 font-weight-normal">Registration</h1>
            {error && <div className="alert alert-danger">{error}</div>}
            {success && <div className="alert alert-success">{success}</div>}
            <form onSubmit={handleSubmit}>
                <label htmlFor="email">Email</label>
                <input type="email" name="email" value={formData.email} onChange={handleChange} required />
                <label htmlFor="plainPassword">Password</label>
                <input type="password" name="plainPassword" value={formData.plainPassword} onChange={handleChange} required />
                <label htmlFor="secondPassword">Repeat Password</label>
                <input type="password" name="secondPassword" value={formData.secondPassword} onChange={handleChange} required />
                <button type="submit">REGISTER</button>
            </form>
        </div>
    );
};

export default Register;
