const $ = require('jquery');
require('bootstrap');
import './styles/app.scss';

function addToFavoritesHandler(event) {
    const button = event.currentTarget;
    const mealType = button.dataset.mealType;
    const mealId = button.dataset.mealId.split('-')[0];

    fetch(`/add-to-favorites/${mealType}/${mealId}`, {
        method: 'POST',
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                button.classList.add('remove-from-favorites');
                button.classList.remove('add-to-favorites');
                button.removeEventListener('click', addToFavoritesHandler);
                button.addEventListener('click', removeFromFavoritesHandler);
            } else {
                console.log(data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
}

function removeFromFavoritesHandler(event) {
    const button = event.currentTarget;
    const mealType = button.dataset.mealType;
    const mealId = button.dataset.mealId.split('-')[0];

    fetch(`/remove-from-favorites/${mealType}/${mealId}`, {
        method: 'POST',
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                button.classList.add('add-to-favorites');
                button.classList.remove('remove-from-favorites');
                button.removeEventListener('click', removeFromFavoritesHandler);
                button.addEventListener('click', addToFavoritesHandler);
            } else {
                console.log(data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
}

document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.favorites-toggle');
    buttons.forEach(button => {
        if (button.classList.contains('add-to-favorites')) {
            button.addEventListener('click', addToFavoritesHandler);
        } else if (button.classList.contains('remove-from-favorites')) {
            button.addEventListener('click', removeFromFavoritesHandler);
        }
    });

    let generatePlanLink = document.querySelector('.generate-plan-link');
    let throbber = document.getElementById('throbber');

    if (generatePlanLink && throbber) {
        generatePlanLink.addEventListener('click', function(e) {
            e.preventDefault();
            throbber.style.display = 'flex';
            setTimeout(function() {
                window.location.href = generatePlanLink.getAttribute('href');
            }, 500);
        });
    }
});

function toggleStepDescription(event) {
    let stepContainer = event.currentTarget;
    let text = stepContainer.querySelector('.step-text');

    text.classList.toggle('hidden');
    stepContainer.classList.toggle('expanded');
    stepContainer.classList.toggle('step-expanded');
}

document.querySelectorAll('.step').forEach(function(step) {
    step.addEventListener('click', toggleStepDescription);
});

document.getElementById('hamburger').addEventListener('click', function() {
    let navMenu = document.getElementById('nav-menu');
    let isOpen = navMenu.classList.contains('active');

    this.setAttribute('aria-expanded', isOpen ? 'false' : 'true');

    navMenu.classList.toggle('active');
    this.classList.toggle('active');
});
