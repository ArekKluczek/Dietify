import axios from 'axios';
import https from 'https';

const httpsAgent = new https.Agent({
    rejectUnauthorized: false
});

const apiClient = axios.create({
    httpsAgent,
    baseURL: 'https://carfix.ddev.site:448/api',
    timeout: 5000,
});

export default apiClient;
