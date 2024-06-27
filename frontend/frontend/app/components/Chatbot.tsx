'use client';
import React, { useState, useEffect, useCallback, useRef } from 'react';
import axios from 'axios';

interface Message {
    text: string;
    sender: 'user' | 'bot';
}

const Chatbot: React.FC = () => {
    const [messages, setMessages] = useState<Message[]>([]);
    const [input, setInput] = useState('');
    const [isOpen, setIsOpen] = useState(false);
    const messagesEndRef = useRef<HTMLDivElement>(null);

    const handleSendMessage = useCallback(async () => {
        if (input.trim() === '') return;

        const userMessage: Message = { text: input, sender: 'user' };
        setMessages(prevMessages => [...prevMessages, userMessage]);

        setInput('');

        const conversation = [
            { role: 'system', content: 'You are a helpful assistant that answers questions related to diet and the app usage.' },
            ...messages.map(message => ({
                role: message.sender === 'user' ? 'user' : 'assistant',
                content: message.text,
            })),
            { role: 'user', content: input },
        ];

        try {
            const response = await axios.post(
                'https://api.openai.com/v1/chat/completions',
                {
                    model: 'gpt-3.5-turbo',
                    messages: conversation
                },
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${process.env.NEXT_PUBLIC_OPENAI_API_KEY}`
                    }
                }
            );
            const botMessage: Message = { text: response.data.choices[0].message.content, sender: 'bot' };
            setMessages(prevMessages => [...prevMessages, botMessage]);
        } catch (error) {
            console.error('Error sending message to chatbot:', error);
            const botErrorMessage: Message = { text: 'Sorry, there was an error processing your request.', sender: 'bot' };
            setMessages(prevMessages => [...prevMessages, botErrorMessage]);
        }
    }, [input, messages]);

    useEffect(() => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages]);

    const toggleChat = () => {
        setIsOpen(!isOpen);
    };

    return (
        <div>
            <div className={`chatbot-icon ${isOpen ? 'hidden' : ''}`} onClick={toggleChat}>
                💬
            </div>
            <div className={`chatbot-container ${isOpen ? 'open' : 'closed'}`}>
                <div className="chatbot-header">
                    <button onClick={toggleChat} className="btn-close"></button>
                </div>
                <div className="chatbot-messages">
                    {messages.map((message, index) => (
                        <div key={index} className={`chatbot-message ${message.sender}`}>
                            {message.text}
                        </div>
                    ))}
                    <div ref={messagesEndRef} />
                </div>
                <div className="chatbot-input-container">
                    <input
                        type="text"
                        value={input}
                        onChange={(e) => setInput(e.target.value)}
                        onKeyDown={(e) => e.key === 'Enter' && handleSendMessage()}
                        placeholder="Ask me anything about diet or the app..."
                    />
                    <button onClick={handleSendMessage}>Send</button>
                </div>
            </div>
        </div>
    );
};

export default Chatbot;
