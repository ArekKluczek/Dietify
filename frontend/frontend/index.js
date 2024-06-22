const axios = require('axios');

axios.get('https://incomplete-chain.badssl.com')
    .then(function (response) {
        console.log(response);
    })
    .catch(function (error) {
        console.log(error);
    });