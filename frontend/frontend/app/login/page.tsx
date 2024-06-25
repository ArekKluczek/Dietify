'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import apiClient from '../apiClient';

const Login = () => {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState(null);
    const router = useRouter();

    const handleSubmit = async (e: { preventDefault: () => void; }) => {
        e.preventDefault();
        try {
            console.log('Submitting login data:', { username, password });
            const response = await apiClient.post('/login', {
                username,
                password,
            });
            console.log('Response:', response);
            if (response.data.status === 'success') {
                localStorage.setItem('token', response.data.token);
                setError(null);
                await router.push('/');
            } else {
                setError(response.data.message);
            }
        } catch (error) {
            // @ts-ignore
            console.error('Error:', error.response || error.message);
            // @ts-ignore
            setError(error.response?.data.message || 'An error occurred. Please try again.');
        }
    };

    return (
        <div className="form-container">
            {error && <div className="alert alert-danger">{error}</div>}
            <form onSubmit={handleSubmit} className="form-signin">
                <h1>Please sign in</h1>
                <label htmlFor="username">Email</label>
                <input
                    type="email"
                    value={username}
                    name="username"
                    id="username"
                    className="form-control"
                    autoComplete="email"
                    required
                    autoFocus
                    onChange={(e) => setUsername(e.target.value)}
                />
                <label htmlFor="password">Password</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    className="form-control"
                    autoComplete="current-password"
                    required
                    onChange={(e) => setPassword(e.target.value)}
                />
                <button type="submit">SIGN IN</button>
            </form>
        </div>
    );
};
export default Login;