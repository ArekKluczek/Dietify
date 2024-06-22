/** @type {import('next').NextConfig} */
const nextConfig = {
    async rewrites() {
        return [
            {
                source: '/api/:path*',
                destination: 'https://127.0.0.1:52374/api/:path*'
            }
        ];
    },
    webpack: (config, { isServer }) => {
        if (isServer) {
            process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0';
        }
        return config;
    }
};

export default nextConfig;
