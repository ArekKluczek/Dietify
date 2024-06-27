'use client'
import React, { useEffect, useState } from 'react';
import apiClient from './services/apiClient';

interface Meal {
    name: string;
    preparation_time: number;
    calories: number;
    carbohydrates: number;
    protein: number;
    uniqueMealId: string;
}

interface Meals {
    breakfast: Meal;
    brunch: Meal;
    lunch: Meal;
    snack: Meal;
    dinner: Meal;
}

interface User {
    id: string;
    name: string;
    email: string;
}

const Dashboard = () => {
    const [meals, setMeals] = useState<Meals | null>(null);
    const [user, setUser] = useState<User | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchUser = async () => {
            const token = localStorage.getItem('token');
            if (token) {
                try {
                    const response = await apiClient.get<User>('/user', {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    });
                    setUser(response.data);
                } catch (error) {
                    console.error('Error fetching user data:', error);
                }
            }
            setLoading(false);
        };

        fetchUser();

        const fetchMeals = async () => {
            try {
                const response = await apiClient.get<Meals | null>('/meals-today');
                setMeals(response.data);
            } catch (error) {
                console.error('Error fetching meals:', error);
            }
        };

        fetchMeals();
    }, []);

    if (loading) {
        return <div>Loading...</div>;
    }

    return (
        <main className="main">
            <div className="about">
                <div className="about__welcome">Welcome to Dietify!</div>
                <div className="about__text">
                    <p>Are you looking to transform your health and well-being through tailored nutrition? Look no further!
                        Our platform is dedicated to crafting personalized diets designed specifically for you.
                        We understand that each individual has unique dietary needs, goals, and preferences,
                        and we're here to help you navigate your nutritional journey with precision and care.</p>
                </div>
                <div className="about__buttons">
                    <a href="/mealPlan" className="btn-view-plans generate-plan-link">VIEW PLANS</a>
                </div>
            </div>
            {user && meals ? (
                <>
                    <h1 className="meals-today">Meals today</h1>
                    <div className="meal-container">
                        {Object.entries(meals).map(([mealTime, meal]) => (
                            <div key={meal.uniqueMealId} className="meal">
                                <div className="meal__type-container">
                                    <div className="meal__type">{mealTime}</div>
                                    <div className="meal__preparation-time">{meal[0].preparation_time}m</div>
                                </div>
                                <div className="meal__details">
                                    <p>{meal[0].name}</p>
                                    <div className="meal__macros">
                                        <p>KCAL: <span className="color-green">{meal[0].calories}</span></p>
                                        <p>C: <span className="color-green">{meal[0].carbohydrates}g</span></p>
                                        <p>P: <span className="color-green">{meal[0].protein}g</span></p>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </>
            ) : (
                <div className="unlogged-section">
                    <h2>Sample Meals</h2>
                    <div className="meal-container">
                        <div className="meal">
                            <div className="meal__name">Breakfast</div>
                            <p className="meal__food">Avocado Toast with Poached Eggs</p>
                        </div>
                        <div className="meal">
                            <div className="meal__name">Lunch</div>
                            <p className="meal__food">Quinoa Salad Bowl with Grilled Chicken</p>
                        </div>
                        <div className="meal">
                            <div className="meal__name">Snack</div>
                            <p className="meal__food">Apple with Almond Butter</p>
                        </div>
                        <div className="meal">
                            <div className="meal__name">Dinner</div>
                            <p className="meal__food">Salmon with Steamed Broccoli and Quinoa</p>
                        </div>
                    </div>
                    <div className="unlogged-section__buttons">
                        <a href="/mealPlan" className="btn-get-your-plan generate-plan-link">GET YOUR PLAN</a>
                        <a href="/howItWorks" className="btn-how-it-works">HOW IT WORKS</a>
                    </div>
                </div>
            )}
        </main>
    );
};

export default Dashboard;
