import React from 'react';
import apiClient from "../services/apiClient";

export const downloadShoppingList = async () => {
    try {
        const response = await apiClient.get('/profile/diet/shopping-list', { responseType: 'blob' });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'shopping_list.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (error) {
        console.error('Error downloading shopping list:', error);
    }
};

const ShoppingList: React.FC = () => {
    return (
        <div>
            <button onClick={downloadShoppingList}>Download Shopping List</button>
        </div>
    );
};

export default ShoppingList;
