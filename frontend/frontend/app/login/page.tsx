'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import apiClient from "../services/apiClient";
import { Formik, Form, Field, ErrorMessage } from 'formik';
import * as Yup from 'yup';
import { AxiosError } from 'axios';

const Login = () => {
    const router = useRouter();
    const [error, setError] = useState<string | null>(null);

    const initialValues = {
        username: '',
        password: '',
    };

    const validationSchema = Yup.object({
        username: Yup.string().email('Invalid email format').required('Email is required'),
        password: Yup.string().required('Password is required'),
    });

    const handleSubmit = async (values: { username: string; password: string }) => {
        try {
            console.log('Submitting login data:', values);
            const response = await apiClient.post('/login', values);
            console.log('Response:', response);
            if (response.data.status === 'success') {
                localStorage.setItem('token', response.data.token);
                setError(null);
                await router.push('/');
            } else {
                setError(response.data.message);
            }
        } catch (err) {
            const error = err as AxiosError<{ message: string }>;
            console.error('Error:', error.response || error.message);
            setError(error.response?.data.message || 'An error occurred. Please try again.');
        }
    };

    return (
        <div className="form-container">
            {error && <div className="alert alert-danger">{error}</div>}
            <Formik
                initialValues={initialValues}
                validationSchema={validationSchema}
                onSubmit={handleSubmit}
            >
                {({ isSubmitting }) => (
                    <Form className="form-signin">
                        <h1>Please sign in</h1>
                        <div>
                            <label htmlFor="username">Email</label>
                            <Field
                                type="email"
                                name="username"
                                id="username"
                                className="form-control"
                                autoComplete="email"
                                required
                            />
                            <ErrorMessage name="username" component="div" className="error-message" />
                        </div>
                        <div>
                            <label htmlFor="password">Password</label>
                            <Field
                                type="password"
                                name="password"
                                id="password"
                                className="form-control"
                                autoComplete="current-password"
                                required
                            />
                            <ErrorMessage name="password" component="div" className="error-message" />
                        </div>
                        <button type="submit" disabled={isSubmitting}>
                            SIGN IN
                        </button>
                    </Form>
                )}
            </Formik>
            <div className="elipse-login"></div>
        </div>
    );
};

export default Login;
