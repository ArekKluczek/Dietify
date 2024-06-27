'use client'
import React, { useEffect, useState } from 'react';
import apiClient from "../services/apiClient";

interface MealDetails {
    name: string;
    calories: number;
    carbohydrates: number;
    protein: number;
}

interface FavouriteMeal {
    mealDetails: MealDetails;
}

interface GroupedFavourites {
    [mealType: string]: FavouriteMeal[];
}

interface User {
    id: string;
    name: string;
    email: string;
}

const Favourites = () => {
    const [groupedFavourites, setGroupedFavourites] = useState<GroupedFavourites | null>(null);
    const [user, setUser] = useState<User | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchFavourites = async () => {
            try {
                const response = await apiClient.get<GroupedFavourites>('/favourites');
                setGroupedFavourites(response.data);
            } catch (error) {
                console.error('Error fetching favourite meals:', error);
            }
        };

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
        };

        fetchFavourites();
        fetchUser();
        setLoading(false);
    }, []);

    if (loading) {
        return <div className="generate-plan-link">Loading...</div>;
    }

    if (!groupedFavourites || !user) {
        return <div>No favourite meals available or user not authenticated.</div>;
    }

    return (
        <main className="main">
            <div className="sidebar">
                <a href="/profile">Profile</a>
                <a href={`/user/${user.id}`}>Settings</a>
                <a href="/favourites">Favourites</a>
                <a href="/forum">Forum</a>
            </div>
            <div className="meal-plan__container-favourite">
                {Object.entries(groupedFavourites).map(([mealType, favourites]) => (
                    <section key={mealType} className="meal-plan__day favourites">
                        <h2 className="meal-plan__day-title favourites__day-title">{mealType.charAt(0).toUpperCase() + mealType.slice(1)}</h2>
                        <div className="meal-plan__meal favourites__meal">
                            {favourites.map((item, index) => (
                                <div key={index} className="meal-plan__meal-details favourites__meal-details">
                                    <div className="meal-plan__meal-name favourites__meal-name">{item.mealDetails.name}</div>
                                    <div className="meal-plan__meal-info">
                                        <span className="meal-plan__meal-calories"><span className="color-green">{item.mealDetails.calories}</span> kcal</span>
                                        <span className="meal-plan__meal-protein"><span className="color-green">{item.mealDetails.protein}</span> protein</span>
                                        <span className="meal-plan__meal-carbs"><span className="color-green">{item.mealDetails.carbohydrates}</span> carbohydrates</span>
                                    </div>
                                    <div className="meal-plan__meal-info-mobile">
                                        <span className="meal-plan__meal-calories"><span className="color-green">{item.mealDetails.calories}</span> kcal</span>
                                        <span className="meal-plan__meal-protein"><span className="color-green">{item.mealDetails.protein}</span> protein</span>
                                        <span className="meal-plan__meal-carbs"><span className="color-green">{item.mealDetails.carbohydrates}</span> carbohydrates</span>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </section>
                ))}
            </div>
        </main>
    );
};

export default Favourites;
