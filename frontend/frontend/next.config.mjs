/** @type {import('next').NextConfig} */
const nextConfig = {
    async rewrites() {
        return [
            {
                source: '/api/:path*',
                destination: 'https://carfix.ddev.site:448/api/:path*'
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
