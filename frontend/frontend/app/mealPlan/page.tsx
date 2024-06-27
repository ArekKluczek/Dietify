'use client'
import React, { useEffect, useState } from 'react';
import apiClient from "../services/apiClient";
import { useRouter } from 'next/navigation';
import axios, { AxiosError } from 'axios';
import { downloadShoppingList } from '../components/shoppingList';

interface Meal {
    name: string;
    calories: number;
    carbohydrates: number;
    protein: number;
    time: string;
    preparation_time: number;
    uniqueMealId: string;
}

interface DayMeals {
    breakfast: Meal[];
    brunch: Meal[];
    lunch: Meal[];
    snack: Meal[];
    dinner: Meal[];
}

interface DietData {
    mealPlans: {
        [day: string]: DayMeals[];
    };
    favoriteMealsIds: string[];
}

const ShowDiet: React.FC = () => {
    const [dietData, setDietData] = useState<DietData | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const router = useRouter();

    useEffect(() => {
        const fetchDiet = async () => {
            try {
                const response = await apiClient.get<DietData>('/profile/diet/show');
                setDietData(response.data);
            } catch (err) {
                const error = err as AxiosError;
                if (error.response) {
                    if (error.response.status === 404) {
                        router.push('/profile');
                    } else {
                        setError('Failed to generate diet plan.');
                    }
                } else {
                    setError('Error fetching diet plan.');
                }
            } finally {
                setLoading(false);
            }
        };

        fetchDiet();
    }, [router]);

    const toggleFavorite = async (uniqueMealId: string, isFavorite: boolean) => {
        const [mealId, mealType] = uniqueMealId.split('-');
        try {
            const url = isFavorite ? `/remove-from-favorites/${mealType}/${mealId}` : `/add-to-favorites/${mealType}/${mealId}`;
            const response = await apiClient.post(url);
            if (response.data.status === 'success') {
                setDietData((prev) => {
                    if (!prev) return prev;
                    const updatedFavorites = isFavorite
                        ? prev.favoriteMealsIds.filter(id => id !== uniqueMealId)
                        : [...prev.favoriteMealsIds, uniqueMealId];

                    return { ...prev, favoriteMealsIds: updatedFavorites };
                });
            } else {
                console.error(response.data.message);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    };

    if (loading) {
        return <div id="throbber" className="throbber-container">
            <div className="spinner"></div>
        </div>;
    }

    if (error) {
        return <div>{error}</div>;
    }

    if (!dietData) {
        return <div>No diet plan available.</div>;
    }

    return (
        <div className="meal-plan__container">
            {Object.entries(dietData.mealPlans).map(([mealDay, mealsOfDay]) => (
                <div key={mealDay} className="meal-plan__day">
                    <div className="meal-plan__day-title">
                        <h2>{mealDay.charAt(0).toUpperCase() + mealDay.slice(1)}</h2>
                    </div>
                    {Object.entries(mealsOfDay[0]).map(([mealType, meals]) => (
                        <div key={mealType} className="meal-plan__meal">
                            <div className="meal-plan__meal-time">{mealType.charAt(0).toUpperCase() + mealType.slice(1)} {meals[0].time}</div>
                            <div className="meal-plan__meal-details">
                                <div className="meal-plan__meal-name">{meals[0].name}</div>
                                <div className="meal-plan__meal-info">
                                    <span className="meal-plan__meal-calories"><span className="color-green">{meals[0].calories}</span> kcal</span>
                                    <span className="meal-plan__meal-protein"><span className="color-green">{meals[0].protein}</span> protein</span>
                                    <span className="meal-plan__meal-carbs"><span className="color-green">{meals[0].carbohydrates}</span> carbohydrates</span>
                                </div>
                                <div className="meal-plan__meal-info-mobile">
                                    <span className="meal-plan__meal-calories-mobile">kcal:<span className="color-green"> {meals[0].calories}</span> </span>
                                    <span className="meal-plan__meal-protein-mobile">P:<span className="color-green"> {meals[0].protein}</span> </span>
                                    <span className="meal-plan__meal-carbs-mobile">C:<span className="color-green"> {meals[0].carbohydrates}</span></span>
                                </div>
                                <button
                                    className={`favorites-toggle ${dietData.favoriteMealsIds.includes(meals.uniqueMealId) ? 'remove-from-favorites' : 'add-to-favorites'}`}
                                    data-meal-type={mealType}
                                    data-meal-id={meals.uniqueMealId}
                                    onClick={() => toggleFavorite(meals.uniqueMealId, dietData.favoriteMealsIds.includes(meals.uniqueMealId))}
                                >
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            ))}
            <div className="meal-plan__links">
                <a href="/mealPlan" className="btn-view-plans generate-plan-link">Generate new plan</a>
                <button onClick={downloadShoppingList} className="shopping-button">Download Shopping List</button>
            </div>
        </div>
    );
};

export default ShowDiet;
