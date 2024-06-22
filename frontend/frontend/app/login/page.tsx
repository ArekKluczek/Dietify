"use client";

import { useState } from 'react';
import axios from 'axios';
import { useRouter } from 'next/navigation';

const Login = () => {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const router = useRouter();

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            const response = await axios.post('https://127.0.0.1:32768/login', {
                username,
                password,
            });
            if (response.data.status === 'success') {
                await router.push('/');
            } else {
                setError(response.data.message);
            }
        } catch (err) {
            setError('An error occurred. Please try again.');
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
                    name="_username"
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
                    name="_password"
                    id="password"
                    className="form-control"
                    autoComplete="current-password"
                    required
                    onChange={(e) => setPassword(e.target.value)}
                />
                <input
                    type="hidden"
                    name="_csrf_token"
                    value="dummy_csrf_token"
                />
                <button type="submit">SIGN IN</button>
            </form>
        </div>
    );
};

export default Login;
