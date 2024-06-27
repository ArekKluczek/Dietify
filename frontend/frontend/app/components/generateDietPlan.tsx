import React from 'react';
import apiClient from "../services/apiClient";
import { useRouter } from 'next/router';

const GenerateDiet: React.FC = () => {
    const router = useRouter();

    const generateDiet = async () => {
        try {
            await apiClient.post('/profile/diet');
            alert('Diet plan generated successfully!');
            router.push('/show-diet');
        } catch (error) {
            console.error('Error generating diet plan:', error);
            alert('Failed to generate diet plan.');
        }
    };

    return (
        <div>
            <button onClick={generateDiet}>Generate Diet Plan</button>
        </div>
    );
};

export default GenerateDiet;