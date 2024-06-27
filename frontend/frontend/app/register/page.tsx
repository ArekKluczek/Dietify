'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import apiClient from "../services/apiClient";
import { Formik, Form, Field, ErrorMessage } from 'formik';
import * as Yup from 'yup';
import { AxiosError } from 'axios';

const Register = () => {
    const router = useRouter();
    const [error, setError] = useState<string | null>(null);
    const [success, setSuccess] = useState<string | null>(null);

    const initialValues = {
        email: '',
        plainPassword: '',
        secondPassword: '',
    };

    const validationSchema = Yup.object({
        email: Yup.string().email('Invalid email format').required('Email is required'),
        plainPassword: Yup.string().required('Password is required'),
        secondPassword: Yup.string()
            .oneOf([Yup.ref('plainPassword')], 'Passwords must match')
            .required('Confirm password is required'),
    });

    const handleSubmit = async (values: { email: string; plainPassword: string; secondPassword: string }) => {
        try {
            console.log('Submitting form data:', values);
            const response = await apiClient.post('/register', values);
            console.log('Response:', response);
            if (response.data.status === 'success') {
                localStorage.setItem('token', response.data.token);
                setError(null);
                setSuccess('Registration successful!');
                await router.push('/');
            } else {
                setError(response.data.message);
            }
        } catch (err) {
            const error = err as AxiosError;
            console.error('Error:', error.response || error.message);
            setError('An error occurred. Please try again.');
            setSuccess(null);
        }
    };

    return (
        <div className="form-container">
            <h1 className="h3 mb-3 font-weight-normal">Registration</h1>
            {error && <div className="alert alert-danger">{error}</div>}
            {success && <div className="alert alert-success">{success}</div>}
            <Formik
                initialValues={initialValues}
                validationSchema={validationSchema}
                onSubmit={handleSubmit}
            >
                {({ isSubmitting }) => (
                    <Form className="registration_form">
                        <div>
                            <label htmlFor="email">Email</label>
                            <Field type="email" name="email" required />
                            <ErrorMessage name="email" component="div" className="error-message" />
                        </div>
                        <div>
                            <label htmlFor="plainPassword">Password</label>
                            <Field type="password" name="plainPassword" required />
                            <ErrorMessage name="plainPassword" component="div" className="error-message" />
                        </div>
                        <div>
                            <label htmlFor="secondPassword">Repeat Password</label>
                            <Field type="password" name="secondPassword" required />
                            <ErrorMessage name="secondPassword" component="div" className="error-message" />
                        </div>
                        <button type="submit" disabled={isSubmitting}>REGISTER</button>
                    </Form>
                )}
            </Formik>
            <div className="elipse-left"></div>
        </div>
    );
};

export default Register;
