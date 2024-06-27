'use client';

import { useEffect, useState } from 'react';
import apiClient from "../services/apiClient";
import { useRouter } from 'next/navigation';

interface BMIResponse {
    bmiValue: number;
    bmiCategory: string;
}

const BMI = () => {
    const [bmiData, setBmiData] = useState<BMIResponse | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const router = useRouter();

    useEffect(() => {
        const fetchBMI = async () => {
            try {
                const response = await apiClient.get<BMIResponse>('/profile/bmi');
                setBmiData(response.data);
            } catch (error: any) {
                if (error.response?.status === 404) {
                    router.push('/profile');
                } else {
                    setError('Error fetching BMI data.');
                }
            } finally {
                setLoading(false);
            }
        };

        fetchBMI();
    }, [router]);

    if (loading) {
        return <div>Loading...</div>;
    }

    if (error) {
        return <div>{error}</div>;
    }

    if (!bmiData) {
        return <div>Error fetching BMI data.</div>;
    }

    return (
        <div className="bmi-container">
            <div className="bmi">
                <h1>Your BMI: <span className="color-green">{bmiData.bmiValue.toFixed(2)}</span> - {bmiData.bmiCategory}</h1>
                <p>
                    BMI, or Body Mass Index, is a measure that uses your height and weight to work out if your weight is in
                    a healthy range.
                    It's a useful tool to identify whether you are underweight, normal weight, overweight, or obese.
                    The calculation divides an adult's weight in kilograms by their height in meters squared.
                    For most adults, an ideal BMI is in the range of 18.5 to 24.9.
                </p>
                <p>
                    In your Dietify profile, your BMI is automatically calculated based on the height and weight you have
                    entered.
                    Whenever you update your profile with new measurements, your BMI will reflect these changes accordingly.
                </p>
                <p>Here are the BMI categories as used in Dietify:</p>
                <div className="categories">
                    <div className="categories__tile-container"><p className="categories__tile">Less than <span className="color-green"> 16 </span>:
                        Severely underweight</p></div>
                    <div className="categories__tile-container"><p className="categories__tile"><span
                        className="color-green"> 16 </span> to <span className="color-green"> 18.5 </span>: Underweight
                    </p></div>
                    <div className="categories__tile-container"><p className="categories__tile"><span
                        className="color-green"> 18.5 </span> to <span className="color-green"> 25 </span>: Normal
                        (healthy weight)</p></div>
                    <div className="categories__tile-container"><p className="categories__tile"><span
                        className="color-green"> 25 </span> to <span className="color-green"> 30 </span>: Overweight</p>
                    </div>
                    <div className="categories__tile-container"><p className="categories__tile"><span
                        className="color-green"> 30 </span> to <span className="color-green"> 35 </span>: Obese Class I
                        (Moderately obese)</p></div>
                    <div className="categories__tile-container"><p className="categories__tile"><span
                        className="color-green"> 35 </span> to <span className="color-green"> 40 </span>: Obese Class II
                        (Severely obese)</p></div>
                    <div className="categories__tile-container"><p className="categories__tile">More than <span className="color-green"> 40 </span>:
                        Obese Class III (Very severely obese)</p></div>
                </div>
                <p>It's important to note that BMI is a simple and useful indicator of a healthy body weight, but it doesn't
                    measure body fat directly and can sometimes be misleading, especially in muscular individuals or those
                    with a health condition that affects body weight. Always consult a healthcare provider for the
                    most comprehensive assessment of your health.</p>
            </div>
        </div>
    );
};

export default BMI;
