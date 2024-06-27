'use client';
import React, { useState, useEffect, useMemo } from 'react';
import { Formik, Form, Field, ErrorMessage } from 'formik';
import * as Yup from 'yup';
import apiClient from '../services/apiClient';
import 'bootstrap/dist/css/bootstrap.min.css';
import {useAuth} from "@/app/services/authContext";

interface Post {
    id: number;
    title: string;
    content: string;
    author: string;
    createdAt: string;
}

const Forum: React.FC = () => {
    const { user } = useAuth();
    const [posts, setPosts] = useState<Post[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchPosts = async () => {
            try {
                const response = await apiClient.get('/posts');
                setPosts(response.data);
            } catch (error) {
                setError('Error fetching posts.');
            } finally {
                setLoading(false);
            }
        };

        fetchPosts();
    }, []);

    const sortedPosts = useMemo(() => {
        return posts.sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime());
    }, [posts]);

    const validationSchema = Yup.object({
        title: Yup.string().required('Title is required'),
        content: Yup.string().required('Content is required'),
        author: Yup.string().required('Author is required'),
    });

    const handleSubmit = async (values: { title: string; content: string; author: string }, { resetForm }: { resetForm: () => void }) => {
        try {
            const response = await apiClient.post('/posts', values);
            setPosts((prevPosts) => [response.data, ...prevPosts]);
            resetForm();
        } catch (error) {
            setError('Error creating post.');
        }
    };

    if (loading) {
        return <div className="text-center my-5">Loading...</div>;
    }

    if (error) {
        return <div className="alert alert-danger">{error}</div>;
    }

    return (
        <div className="container">
            <h1 className="my-5 text-center">Forum</h1>
            <Formik
                initialValues={{ title: '', content: '', author: user?.email || '' }}
                validationSchema={validationSchema}
                onSubmit={handleSubmit}
                enableReinitialize
            >
                {({ isSubmitting }) => (
                    <Form className="mb-5">
                        <div className="mb-3">
                            <Field type="text" name="title" className="form-control" placeholder="Title" required />
                            <ErrorMessage name="title" component="div" className="text-danger" />
                        </div>
                        <div className="mb-3">
                            <Field as="textarea" name="content" className="form-control" placeholder="Content" required />
                            <ErrorMessage name="content" component="div" className="text-danger" />
                        </div>
                        <div className="mb-3">
                            <Field type="text" name="author" className="form-control" disabled value={user?.email || ''} required />
                            <ErrorMessage name="author" component="div" className="text-danger" />
                        </div>
                        <button type="submit" className="btn btn-success" disabled={isSubmitting}>Post</button>
                    </Form>
                )}
            </Formik>
            <div className="posts">
                {sortedPosts.map((post) => (
                    <div key={post.id} className="card mb-3">
                        <div className="card-body">
                            <h2 className="card-title">{post.title}</h2>
                            <p className="card-text">{post.content}</p>
                            <div className="post-meta">
                                <span>By {post.author}</span> | <span>{new Date(post.createdAt).toLocaleString()}</span>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default Forum;
