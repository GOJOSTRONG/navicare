// server.js
const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');
const { exec } = require('child_process');
const app = express();

app.use(cors());
app.use(bodyParser.json());

// Use Ollama's HTTP API
app.post('/api/ask', async (req, res) => {
    const question = req.body.question;

    try {
        const response = await fetch('http://localhost:11434/api/generate', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                model: 'WVSU-Navicare-AI:latest',
                prompt: question,
                stream: false
            })
        });

        const data = await response.json();
        res.json({ answer: data.response });
    } catch (err) {
        console.error(err);
        res.status(500).json({ error: 'Failed to get response from Ollama.' });
    }
});

const PORT = 3000;
app.listen(PORT, () => {
    console.log(`Server running on http://localhost:${PORT}`);
});
