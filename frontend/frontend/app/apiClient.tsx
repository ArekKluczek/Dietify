import axios from 'axios';
import https from 'https';

const httpsAgent = new https.Agent({
    rejectUnauthorized: false
});

const apiClient = axios.create({
    httpsAgent,
    baseURL: 'https://127.0.0.1:52374/api',
    timeout: 5000
});

export default apiClient;
