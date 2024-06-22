"use client";
import { useState } from 'react';
import "../globals.scss";

const steps = [
    { id: "how-it-works", title: "Step 1: Your Personal Profile", text: "Start your journey by creating a personal profile. Dietify's seamless interface makes it easy to enter your dietary preferences, allergies, fitness goals, and more. Customize your nutrition plan, track progress, avoid restricted foods, and receive tailored meal suggestions." },
    { id: "ai-integration", title: "Step 2: AI Integration", text: "After you've input your details, it's time for our cutting-edge tech to take the stage. Dietify utilizes the advanced capabilities of GPT-4 to sift through your data. By leveraging OpenAI's latest AI model, we ensure that every meal plan is not only customized but also optimized for your nutritional needs." },
    { id: "generating", title: "Step 3: Generating Deliciousness", text: "With the power of GPT-4, Dietify goes to work creating a variety of meal options that are bound to satisfy your taste buds and support your health objectives. Our AI sifts through thousands of recipes and dietary combinations to design a meal plan that's as unique as you are." },
    { id: "learning", title: "Step 4: Continuous Learning", text: "Our AI doesn't just stop at creating a meal plan. It learns from your feedback and choices, refining and improving the meal suggestions with every interaction. As your tastes evolve or your goals shift, Dietify adapts, ensuring your meal plans remain deliciously relevant." },
    { id: "enjoy-meals", title: "Step 5: Enjoy Your Meals", text: "Once your custom meal plan is set, all that's left is for you to enjoy the mouthwatering meals. Each recipe is crafted to be easy-to-follow, ensuring you spend less time cooking and more time enjoying the flavors. Dietify turns healthy eating into a delight, not a chore." },
];

const HowItWorksComponent = () => {
    const [expandedStep, setExpandedStep] = useState(null);

    const toggleStepDescription = (stepId) => {
        setExpandedStep(expandedStep === stepId ? null : stepId);
    };

    return (
        <main className="main">
            <div className="how-it-works-container">
                <h1 className="how-it-works-title">How It Works</h1>
                {steps.map(step => (
                    <div key={step.id} className="how-it-works-wrapper">
                        <div id={step.id} className={`step step-toggle ${expandedStep === step.id ? 'expanded step-expanded' : ''}`} onClick={() => toggleStepDescription(step.id)}>
                            <p className={`step-title ${expandedStep === step.id ? 'chevron-rotated' : ''}`}>{step.title}</p>
                            <p className={`step-text ${expandedStep === step.id ? '' : 'hidden'}`}>{step.text}</p>
                        </div>
                    </div>
                ))}
            </div>
            <div className="elipse"></div>
        </main>
    );
};

export default HowItWorksComponent;
