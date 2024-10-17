
"use client";

import { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';

export default function Play() {
    const [question, setQuestion] = useState<string>('');
    const [answer, setAnswer] = useState<string>('');
    const [timeLeft, setTimeLeft] = useState<number>(3);
    const [score, setScore] = useState<number>(0);
    const [gameOver, setGameOver] = useState<boolean>(false);
    const router = useRouter();

    useEffect(() => {
        generateQuestion();
    }, []);

    useEffect(() => {
        if (timeLeft > 0) {
            const timer = setTimeout(() => setTimeLeft(timeLeft - 1), 1000);
            return () => clearTimeout(timer);
        } else {
            setGameOver(true);
        }
    }, [timeLeft]);

    const generateQuestion = () => {
        const num1 = Math.floor(Math.random() * 10) + 1;
        const num2 = Math.floor(Math.random() * 10) + 1;
        setQuestion(`${num1} + ${num2}`);
        setAnswer('');
        setTimeLeft(3);
    };

    const handleSubmit = () => {
        const [num1, , num2] = question.split(' ');
        const correctAnswer = parseInt(num1) + parseInt(num2);

        if (parseInt(answer) === correctAnswer) {
            const newScore = score + 1;
            setScore(newScore);

            if (newScore >= 10000) {
                router.push('/success');
            } else {
                generateQuestion();
            }
        } else {
            setGameOver(true);
        }
    };

    const handleRestart = () => {
        setScore(0);
        setGameOver(false);
        generateQuestion();
    };

    if (gameOver) {
        return (
            <div>
                <h1>Game Over!</h1>
                <p>Your Score: {score}</p>
                <button onClick={handleRestart}>Restart</button>
                <button onClick={() => router.push('/')}>Back to Home</button>
            </div>
        );
    }

    return (
        <div>
            <h1>Math Game</h1>
            <p>Time Left: {timeLeft}s</p>
            <p>{question}</p>
            <input
                type="number"
                value={answer}
                onChange={(e) => setAnswer(e.target.value)}
            />
            <button onClick={handleSubmit}>Submit</button>
            <p>Score: {score}</p>
        </div>
    );
}
